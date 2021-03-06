<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\saml;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML2\XML\saml\Assertion;
use SimpleSAML\SAML2\XML\saml\EncryptedAssertion;
use SimpleSAML\SAML2\XML\saml\Issuer;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\xenc\CipherData;
use SimpleSAML\XMLSecurity\XML\xenc\DataReference;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedData;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod;
use SimpleSAML\XMLSecurity\XML\xenc\ReferenceList;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

/**
 * Class \SAML2\EncryptedAssertionTest
 *
 * @package simplesamlphp/saml2
 * @covers \SimpleSAML\SAML2\XML\saml\EncryptedAssertion
 * @covers \SimpleSAML\SAML2\XML\saml\AbstractSamlElement
 */
final class EncryptedAssertionTest extends TestCase
{
    /** @var \DOMDocument */
    private DOMDocument $document;


    /**
     */
    public function setUp(): void
    {
        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/resources/xml/saml_EncryptedAssertion.xml'
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $ed = new EncryptedData(
            new CipherData('GaYev...'),
            null,
            'http://www.w3.org/2001/04/xmlenc#Element',
            null,
            null,
            new EncryptionMethod('http://www.w3.org/2001/04/xmlenc#aes128-cbc'),
            new KeyInfo([new Chunk(DOMDocumentFactory::fromFile(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/vendor/simplesamlphp/xml-security/tests/resources/xml/ds_RetrievalMethod.xml')->documentElement)])
        );
        $encryptedAssertion = new EncryptedAssertion($ed, []);

        $ed = $encryptedAssertion->getEncryptedData();
        $this->assertEquals('GaYev...', $ed->getCipherData()->getCipherValue());
        $this->assertEquals('http://www.w3.org/2001/04/xmlenc#Element', $ed->getType());
        $encMethod = $ed->getEncryptionMethod();
        $this->assertInstanceOf(EncryptionMethod::class, $encMethod);
        $this->assertEquals('http://www.w3.org/2001/04/xmlenc#aes128-cbc', $encMethod->getAlgorithm());
        $this->assertInstanceOf(KeyInfo::class, $ed->getKeyInfo());

        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval($encryptedAssertion)
        );
    }


    /**
     * Test encryption / decryption
     */
    public function testEncryption(): void
    {
        $this->markTestSkipped('This test can be enabled as soon as the rewrite-assertion branch has been merged');

        $pubkey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'public']);
        $pubkey->loadKey(PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::PUBLIC_KEY));

        $assertion = new Assertion(new Issuer('Test'));

        $encAssertion = EncryptedAssertion::fromUnencryptedElement($assertion, $pubkey);
        $doc = DOMDocumentFactory::fromString(strval($encAssertion));

        /** @psalm-var \SimpleSAML\XMLSecurity\XML\EncryptedElementInterface $encAssertion */
        $encAssertion = Assertion::fromXML($doc->documentElement);
        $privkey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
        $privkey->loadKey(PEMCertificatesMock::getPlainPrivateKey(PEMCertificatesMock::PRIVATE_KEY));

        $this->assertEquals(strval($assertion), strval($encAssertion->decrypt($privkey)));
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(EncryptedAssertion::fromXML($this->document->documentElement))))
        );
    }
}
