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
 * @package   NFePHP\Common\Signner
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\Common\Certificate\PublicKey;
use NFePHP\Common\Exception\SignnerException;
use DOMDocument;
use DOMElement;

class Signner
{
    /**
     * Make Singature tag
     * @param string $content
     * @param string $tagname
     * @param string $marker for URI
     * @param string $algorithm
     * @return string
     * @throws \NFePHP\Common\Exception\SignnerException
     */
    public static function sign(
        Certificate $certificate,
        $content,
        $tagname = '',
        $mark = 'Id',
        $algorithm = OPENSSL_ALGO_SHA1
    ) {
        $content = str_replace(
            [
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>",
                "<?xml version=\"1.0\" encoding=\"utf-8\"?>",
                "\r",
                "\n"
            ],
            '',
            $content
        );
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($content);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $root = $dom->documentElement;
        $node = $dom->getElementsByTagName($tagname)->item(0);
        if (empty($node)) {
            throw SignnerException::tagNotFound();
            ;
        }
        if (! self::signatureExists($dom)) {
            $xml = self::createSignature(
                $certificate,
                $dom,
                $root,
                $node,
                $mark,
                $algorithm
            );
        }
        return $xml;
    }

    /**
     * Verify if xml signature is valid
     * @param string $content xml content
     * @param string $tagname tag for sign
     * @return boolean
     */
    public static function signatureValidator($content, $tagname)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($content);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $flag = false;
        if (self::signatureExists($dom)) {
            $flag = self::digestCheck($content, $tagname);
            $flag &= self::signatureCheck($dom);
        }
        return $flag;
    }
    
    /**
     * Method that provides the signature of xml as standard SEFAZ
     * @param \DOMDocument $xmldoc
     * @param \DOMElement $node
     * @param string $marker
     * @param string $algorithm
     * @return string xml signed
     */
    private static function createSignature(
        Certificate $certificate,
        DOMDocument $dom,
        DOMElement $root,
        DOMElement $node,
        $mark,
        $algorithm = OPENSSL_ALGO_SHA1
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
        $digestValue = self::calculeDigest($root, $digestAlgorithm);
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
        $c14n = $signedInfoNode->C14N(false, false, null, null);
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
        return str_replace('<?xml version="1.0"?>', '', $dom->saveXML());
    }

    /**
     * Check if Signature tag already exists
     * @param \DOMDocument $dom
     * @return boolean
     */
    private static function signatureExists(DOMDocument $dom)
    {
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        if (! isset($signature)) {
            return false;
        }
        return true;
    }
    
    /**
     * Verify signature value
     * @param \DOMDocument $dom
     * @return boolean
     * @throws \NFePHP\Common\Exception\SignnerException
     * @throws \NFePHP\Common\Exception\CertificateException
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
        $certificate = $signature->getElementsByTagName('X509Certificate')->item(0)->nodeValue;
        $certificate =  "-----BEGIN CERTIFICATE-----\n"
            . self::splitLines($certificate)
            . "\n-----END CERTIFICATE-----\n";
        $publicKey = new Certificate\PublicKey($certificate);
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
     * @throws \NFePHP\Common\Exception\SignnerException
     */
    private static function digestCheck($content, $tagid = '')
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($content);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $root = $dom->documentElement;
        $node = $dom->getElementsByTagName($tagid)->item(0);
        if (empty($node)) {
            throw SignnerException::tagNotFound();
        }
        $signature = $node->getElementsByTagName('Signature')->item(0);
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
        $calculatedDigest = self::calculeDigest($node, $algorithm);
        $informedDigest = $signature->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        if ($calculatedDigest != $informedDigest) {
            throw SignnerException::digestComparisonFailed();
        }
        return true;
    }
    
    /**
     * Calculate digest value for given node
     * @param \DOMElement $node
     * @param string $algorithm
     * @return string
     */
    private static function calculeDigest(DOMElement $node, $algorithm)
    {
        $c14n = $node->C14N(false, false, null, null);
        $hashValue = hash($algorithm, $c14n, true);
        return base64_encode($hashValue);
    }
    
    /**
     * splitLines
     * Split a string into lines with 76 characters (original standatd)
     * @param string $certificateunformated
     * @return string
     */
    private static function splitLines($certificateunformated)
    {
        return rtrim(chunk_split(str_replace(["\r", "\n"], '', $certificateunformated), 76, "\n"));
    }
}
