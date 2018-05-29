<?php

namespace NFePHP\Common;

/**
 * Class to signner a Xml
 * Meets packages:
 *     sped-nfe,
 *     sped-cte,
 *     sped-mdfe,
 *     sped-nfse,
 *     sped-efinanceira
 *     sped-esocial
 *     sped-efdreinf
 *     e sped-esfinge
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Signer
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Certificate\PublicKey;
use NFePHP\Common\Exception\SignerException;
use DOMDocument;
use DOMNode;
use DOMElement;

class Signer
{
    const CANONICAL = array(true,false,null,null);
    private static $canonical = self::CANONICAL;

    /**
     * Make Signature for multiple tags within the document.
     *
     * @param Certificate $certificate
     * @param \DOMDocument $doc The XML to sign.
     * @param array $tags the tags to include signature.
     * @param string $mark for URI (optional).
     * @param int $algorithm (optional).
     * @param array $canonical parameters to format node for signature (optional).
     *
     * @return \DOMDocument
     *
     * @throws SignerException
     */
    public static function signMultipleTags(
        Certificate $certificate,
        DOMDocument $doc,
        array $tags,
        string $mark = 'Id',
        int $algorithm = OPENSSL_ALGO_SHA1,
        $canonical = self::CANONICAL
    ): DOMDocument
    {
        $signed = $doc;

        foreach ($tags as $tagName) {
            $node = $signed->getElementsByTagName($tagName)->item(0); //TODO: loop here too when multiple tags found.
            
            $signed = self::sign(
                $certificate,
                $signed,
                $node,
                $mark,
                $algorithm,
                $canonical
            );

            if (!Signer::isSigned($signed->saveXML(), $tagName)) {
                throw SignerException::signatureComparisonFailed();
            }
        }

        return $signed;
    }
    
    /**
     * Make the Signature tag.
     *
     * @param Certificate $certificate
     * @param \DOMDocument $content xml to sign
     * @param \DOMNode $tagNode The tagNode to be signed
     * @param string $mark for URI (optional)
     * @param int $algorithm (optional)
     * @param array $canonical parameters to format node for signature (optional)
     *
     * @return \DOMDocument
     *
     * @throws SignerException
     */
    public static function sign(
        Certificate $certificate,
        DOMDocument $content,
        DOMNode $tagNode,
        string $mark = 'Id',
        int $algorithm = OPENSSL_ALGO_SHA1,
        $canonical = self::CANONICAL
    ): DOMDocument
    {
        if (!empty($canonical)) {
            self::$canonical = $canonical;
        }
        
        return self::createSignature(
            $certificate,
            $content,
            $tagNode,
            $mark,
            $algorithm,
            $canonical
        );
    }
    
    /**
     * Method that provides the signature of xml as standard SEFAZ.
     *
     * @param Certificate $certificate
     * @param \DOMDocument $dom The original document
     * @param \DOMElement $node node to be signed
     * @param string $mark Marker signed attribute
     * @param int $algorithm cryptographic algorithm (optional)
     * @param array $canonical parameters to format node for signature (optional)
     *
     * @return \DOMDocument
     */
    private static function createSignature(
        Certificate $certificate,
        DOMDocument $dom,
        DOMElement $node,
        string $mark,
        int $algorithm = OPENSSL_ALGO_SHA1,
        $canonical = self::CANONICAL
    ): DOMDocument {
        $nsDSIG = 'http://www.w3.org/2000/09/xmldsig#';
        $nsCannonMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsSignatureMethod = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
        $nsDigestMethod = 'http://www.w3.org/2000/09/xmldsig#sha1';
        $digestAlgorithm = 'sha1';
        if ($algorithm == OPENSSL_ALGO_SHA256) {
            $digestAlgorithm = 'sha256';
            $nsSignatureMethod = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
            $nsDigestMethod = 'http://www.w3.org/2001/04/xmlenc#sha256';
        }
        $nsTransformMethod1 ='http://www.w3.org/2000/09/xmldsig#enveloped-signature';
        $nsTransformMethod2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $idSigned = trim($node->getAttribute($mark));
        $signatureNode = $dom->createElementNS($nsDSIG, 'Signature');
        $signatureNode->setAttribute('Id', "Ass_$idSigned");
        $node->parentNode->appendChild($signatureNode);
        $signedInfoNode = $dom->createElement('SignedInfo');
        $signatureNode->appendChild($signedInfoNode);
        $canonicalNode = $dom->createElement('CanonicalizationMethod');
        $signedInfoNode->appendChild($canonicalNode);
        $canonicalNode->setAttribute('Algorithm', $nsCannonMethod);
        $signatureMethodNode = $dom->createElement('SignatureMethod');
        $signedInfoNode->appendChild($signatureMethodNode);
        $signatureMethodNode->setAttribute('Algorithm', $nsSignatureMethod);
        $referenceNode = $dom->createElement('Reference');
        $signedInfoNode->appendChild($referenceNode);
        
        if (!empty($idSigned)) {
            $idSigned = "#$idSigned";
        }
        
        $referenceNode->setAttribute('URI', $idSigned);
        $transformsNode = $dom->createElement('Transforms');
        $referenceNode->appendChild($transformsNode);
        $transfNode1 = $dom->createElement('Transform');
        $transformsNode->appendChild($transfNode1);
        $transfNode1->setAttribute('Algorithm', $nsTransformMethod1);
        $transfNode2 = $dom->createElement('Transform');
        $transformsNode->appendChild($transfNode2);
        $transfNode2->setAttribute('Algorithm', $nsTransformMethod2);
        $digestMethodNode = $dom->createElement('DigestMethod');
        $referenceNode->appendChild($digestMethodNode);
        $digestMethodNode->setAttribute('Algorithm', $nsDigestMethod);
        $digestValue = self::makeDigest($node, $digestAlgorithm, $canonical);
        $digestValueNode = $dom->createElement('DigestValue', $digestValue);
        $referenceNode->appendChild($digestValueNode);
        $c14n = self::canonize($signedInfoNode, $canonical);
        $signature = $certificate->sign($c14n, $algorithm);
        $signatureValue = base64_encode($signature);
        $signatureValueNode = $dom->createElement('SignatureValue', $signatureValue);
        $signatureNode->appendChild($signatureValueNode);
        $keyInfoNode = $dom->createElement('KeyInfo');
        $signatureNode->appendChild($keyInfoNode);
        $x509DataNode = $dom->createElement('X509Data');
        $keyInfoNode->appendChild($x509DataNode);
        $pubKeyClean = $certificate->publicKey->unFormated();
        $x509CertificateNode = $dom->createElement('X509Certificate', $pubKeyClean);
        $x509DataNode->appendChild($x509CertificateNode);

        return $dom;
    }

    /**
     * Remove old signature from document to replace it
     * @param string $content
     * @return string
     */
    public static function removeSignature(string $content): string
    {
        if (!self::existsSignature($content)) {
            return $content;
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($content);
        $node = $dom->documentElement;
        $signature = $node->getElementsByTagName('Signature')->item(0);

        if (!empty($signature)) {
            $parent = $signature->parentNode;
            $parent->removeChild($signature);
        }

        return $dom->saveXML();
    }

    /**
     * Verify if xml signature is valid
     * @param string $content
     * @param string $tagname tag for sign (optional)
     * @param array $canonical parameters to format node for signature (optional)
     * @return bool
     * @throws SignerException Not is a XML, Digest or Signature dont match
     */
    public static function isSigned(string $content, string $tagname = '', $canonical = self::CANONICAL): bool
    {
        if (!self::existsSignature($content)) {
            return false;
        }

        if (!self::digestCheck($content, $tagname, $canonical)) {
            return false;
        }

        return self::signatureCheck($content, $canonical);
    }
    
    /**
     * Check if Signature tag already exists
     * @param string $content
     * @return boolean
     */
    public static function existsSignature(string $content): bool
    {
        if (!Validator::isXML($content)) {
            throw SignerException::isNotXml();
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($content);
        $signature = $dom->getElementsByTagName('Signature')->item(0);

        return !empty($signature);
    }
    
    /**
     * Verify signature value from SignatureInfo node and public key
     * @param string $xml
     * @param array $canonical
     * @return boolean
     */
    private static function signatureCheck(string $xml, $canonical = self::CANONICAL): bool
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        $sigMethAlgo = $signature->getElementsByTagName('SignatureMethod')->item(0)->getAttribute('Algorithm');
        $algorithm = OPENSSL_ALGO_SHA256;

        if ($sigMethAlgo == 'http://www.w3.org/2000/09/xmldsig#rsa-sha1') {
            $algorithm = OPENSSL_ALGO_SHA1;
        }

        $certificateContent = $signature->getElementsByTagName('X509Certificate')->item(0)->nodeValue;
        $publicKey = PublicKey::createFromContent($certificateContent);
        $signInfoNode = self::canonize(
            $signature->getElementsByTagName('SignedInfo')->item(0),
            $canonical
        );
        $signatureValue = $signature->getElementsByTagName('SignatureValue')->item(0)->nodeValue;
        $decodedSignature = base64_decode(
            str_replace(array("\r", "\n"), '', $signatureValue)
        );

        if (!$publicKey->verify($signInfoNode, $decodedSignature, $algorithm)) {
            throw SignerException::signatureComparisonFailed();
        }

        return true;
    }
    
    /**
     * Verify digest value of data node
     * @param string $xml
     * @param string $tagname
     * @param array $canonical
     * @return bool
     * @throws SignerException
     */
    private static function digestCheck(string $xml, string $tagname = '', $canonical = self::CANONICAL): bool
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);

        $signature = null;
        $sigURI = null;
        $node = null;

        if (empty($tagname)) {
            $signature = $dom->getElementsByTagName('Signature')->item(0);
            $sigURI = $signature->getElementsByTagName('Reference')->item(0)->getAttribute('URI');
            if (empty($sigURI)) {
                $tagname = $dom->documentElement->nodeName;
            } else {
                $xpath = new \DOMXPath($dom);
                $entries = $xpath->query('//@Id');
                foreach ($entries as $entry) {
                    $tagname = $entry->ownerElement->nodeName;
                    break;
                } 
            }
            $node = $dom->getElementsByTagName($tagname)->item(0);            
            if (empty($node)) {
                throw SignerException::tagNotFound($tagname);
            }
        }
        else {
            $node = $dom->getElementsByTagName($tagname)->item(0);
            if (empty($node)) {
                throw SignerException::tagNotFound($tagname);
            }
            
            $signature = $node->nextSibling;
            if ($signature->nodeName !== 'Signature') {
                throw SignerException::tagNotFound('Signature');
            }
            
            $sigURI = $signature->getElementsByTagName('Reference')->item(0)->getAttribute('URI');
        }

        $sigMethAlgo = $signature->getElementsByTagName('SignatureMethod')->item(0)->getAttribute('Algorithm');
        $algorithm = 'sha256';

        if ($sigMethAlgo == 'http://www.w3.org/2000/09/xmldsig#rsa-sha1') {
            $algorithm = 'sha1';
        }

        if ($sigURI == '') {
            $node->removeChild($signature);
        }

        $calculatedDigest = self::makeDigest($node, $algorithm, $canonical);
        $informedDigest = $signature->getElementsByTagName('DigestValue')->item(0)->nodeValue;

        if ($calculatedDigest != $informedDigest) {
            throw SignerException::digestComparisonFailed();
        }
        
        return true;
    }
    
    /**
     * Calculate digest value for given node
     * @param DOMNode $node
     * @param string $algorithm
     * @param array $canonical
     * @return string
     */
    private static function makeDigest(DOMNode $node, string $algorithm, $canonical = self::CANONICAL): string
    {
        $c14n = self::canonize($node, $canonical);
        $hashValue = hash($algorithm, $c14n, true);

        return base64_encode($hashValue);
    }
    
    /**
     * Reduced to the canonical form
     * @param DOMNode $node
     * @param array $canonical
     * @return string
     */
    private static function canonize(DOMNode $node, $canonical = self::CANONICAL): string
    {
        return $node->C14N(
            $canonical[0],
            $canonical[1],
            $canonical[2],
            $canonical[3]
        );
    }
}
