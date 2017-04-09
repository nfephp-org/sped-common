<?php

namespace NFePHP\Common;

/**
 * Class to signner a Xml
 * Meets packages :
 *     sped-nfe,
 *     sped-cte,
 *     sped-mdfe,
 *     sped-nfse,
 *     sped-efinanceira
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

use NFePHP\Common\Certificate;
use NFePHP\Common\Certificate\PublicKey;
use NFePHP\Common\Strings;
use NFePHP\Common\Exception\SignnerException;
use DOMDocument;
use DOMElement;

class Signer
{
    private static $canonical = [false,false,null,null];
    
    /**
     * Make Signature tag
     * @param string $content
     * @param string $tagname
     * @param string $marker for URI
     * @param string $algorithm
     * @param array $canonical parameters to format node for signature
     * @param string $rootname name of tag to insert signature block
     * @return string
     * @throws \NFePHP\Common\Exception\SignnerException
     */
    public static function sign(
        Certificate $certificate,
        $content,
        $tagname = '',
        $mark = 'Id',
        $algorithm = OPENSSL_ALGO_SHA1,
        $canonical = [false,false,null,null],
        $rootname = ''
    ) {
        //$content = Strings::clearXmlString($content, true);
        if (!empty($canonical)) {
            self::$canonical = $canonical;
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($content);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $root = $dom->documentElement;
        if (!empty($rootname)) {
            $root = $dom->getElementsByTagName($rootname)->item(0);
        }
        $node = $dom->getElementsByTagName($tagname)->item(0);
        if (empty($node) || empty($root)) {
            throw SignerException::tagNotFound($tagname . ' ' . $rootname);
        }
       
        if (! self::existsSignature($dom)) {
            $dom = self::createSignature(
                $certificate,
                $dom,
                $root,
                $node,
                $mark,
                $algorithm,
                $canonical
            );
        };
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
            . $dom->saveXML($dom->documentElement, LIBXML_NOXMLDECL);
    }
    
    /**
     * Remove old signature from document to replace it
     * @param DOMDocument $dom
     * @return DOMDocument
     */
    public static function removeSignature(DOMDocument $dom)
    {
        $node = $dom->documentElement;
        $signature = $node->getElementsByTagName('Signature')->item(0);
        if (!empty($signature)) {
            $parent = $signature->parentNode;
            $oldsignature = $parent->removeChild($signature);
        }
        return $dom;
    }
    
    /**
     * Verify if xml signature is valid
     * @param string $content xml content
     * @param string $tagname tag for sign
     * @return boolean
     */
    public static function isSigned(DOMDocument $dom, $tagname)
    {
        if (self::existsSignature($dom)) {
            self::digestCheck($dom, $tagname);
            self::signatureCheck($dom);
        }
        return true;
    }
    
    /**
     * Method that provides the signature of xml as standard SEFAZ
     * @param Certificate $certificate
     * @param \DOMDocument $dom
     * @param \DOMElement $root xml root
     * @param \DOMElement $node node to be signed
     * @param string $mark Marker signed attribute
     * @param int $algorithm cryptographic algorithm
     * @param array $canonical parameters to format node for signature
     * @return \DOMDocument
     */
    private static function createSignature(
        Certificate $certificate,
        DOMDocument $dom,
        DOMElement $root,
        DOMElement $node,
        $mark,
        $algorithm = OPENSSL_ALGO_SHA1,
        $canonical = [false,false,null,null]
    ) {
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
        $digestValue = self::makeDigest($node, $digestAlgorithm, $canonical);
        $signatureNode = $dom->createElementNS($nsDSIG, 'Signature');
        $root->appendChild($signatureNode);
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
        $digestValueNode = $dom->createElement('DigestValue', $digestValue);
        $referenceNode->appendChild($digestValueNode);
        $c14n = $signedInfoNode->C14N(
            $canonical[0],
            $canonical[1],
            $canonical[2],
            $canonical[3]
        );
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
     * Check if Signature tag already exists
     * @param \DOMDocument $dom
     * @return boolean
     */
    private static function existsSignature(DOMDocument $dom)
    {
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        if (!isset($signature)) {
            return false;
        }
        return true;
    }
    
    /**
     * Verify signature value
     * @param \DOMDocument $dom
     * @return boolean
     */
    private static function signatureCheck(DOMDocument $dom)
    {
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        $sigMethAlgo = $signature->getElementsByTagName('SignatureMethod')->item(0)->getAttribute('Algorithm');
        if ($sigMethAlgo == 'http://www.w3.org/2000/09/xmldsig#rsa-sha1') {
            $algorithm = OPENSSL_ALGO_SHA1;
        } else {
            $algorithm = OPENSSL_ALGO_SHA256;
        }
        $certificateContent = $signature->getElementsByTagName('X509Certificate')->item(0)->nodeValue;
        $publicKey = PublicKey::createFromContent($certificateContent);
        $signContent = $signature->getElementsByTagName('SignedInfo')->item(0)->C14N(true, false, null, null);
        $signatureValue = $signature->getElementsByTagName('SignatureValue')->item(0)->nodeValue;
        $decodedSignature = base64_decode(str_replace(array("\r", "\n"), '', $signatureValue));
        return $publicKey->verify($signContent, $decodedSignature, $algorithm);
    }
    
    /**
     * digestCheck
     * Verify digest value
     * @param string $content
     * @param string $tagid
     * @return boolean
     * @throws \NFePHP\Common\Exception\SignerException
     */
    private static function digestCheck(DOMDocument $dom, $tagname = '')
    {
        $root = $dom->documentElement;
        $node = $dom->getElementsByTagName($tagname)->item(0);
        if (empty($node)) {
            throw SignnerException::tagNotFound($tagname);
        }
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        if (! empty($signature)) {
            $clone = $signature->cloneNode(true);
        } else {
            $signature = $dom->getElementsByTagName('Signature')->item(0);
        }
        $sigMethAlgo = $signature->getElementsByTagName('SignatureMethod')->item(0)->getAttribute('Algorithm');
        $algorithm = 'sha256';
        if ($sigMethAlgo == 'http://www.w3.org/2000/09/xmldsig#rsa-sha1') {
            $algorithm = 'sha1';
        }
        $sigURI = $signature->getElementsByTagName('Reference')->item(0)->getAttribute('URI');
        if ($sigURI == '') {
            $node->removeChild($signature);
        }
        $calculatedDigest = self::makeDigest($node, $algorithm);
        $informedDigest = $signature->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        if ($calculatedDigest != $informedDigest) {
            throw SignerException::digestComparisonFailed();
        }
        return true;
    }
    
    /**
     * Calculate digest value for given node
     * @param \DOMElement $node
     * @param string $algorithm
     * @param array $canonical
     * @return string
     */
    private static function makeDigest(DOMElement $node, $algorithm, $canonical = [false,false,null,null])
    {
        $dados = $node->C14N(true, false, null, null);
        //calcular o hash dos dados
        $hValue = hash('sha1', $dados, true);
        $bH = base64_encode($hValue);
        $c14n = $node->C14N(
            $canonical[0],
            $canonical[1],
            $canonical[2],
            $canonical[3]
        );
        $hashValue = hash($algorithm, $c14n, true);
        $bHv= base64_encode($hashValue);
        
        return base64_encode($hashValue);
    }
}
