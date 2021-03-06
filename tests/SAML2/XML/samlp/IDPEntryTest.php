<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\samlp;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML2\XML\samlp\IDPEntry;
use SimpleSAML\XML\DOMDocumentFactory;

/**
 * Class \SAML2\XML\samlp\IDPEntryTest
 *
 * @covers \SimpleSAML\SAML2\XML\samlp\IDPEntry
 * @covers \SimpleSAML\SAML2\XML\samlp\AbstractSamlpElement
 *
 * @package simplesamlphp/saml2
 */
final class IDPEntryTest extends TestCase
{
    /** @var \DOMDocument */
    private DOMDocument $document;


    /**
     */
    protected function setUp(): void
    {
        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/resources/xml/samlp_IDPEntry.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $entry = new IDPEntry('urn:some:requester', 'testName', 'testLoc');

        $this->assertEquals('urn:some:requester', $entry->getProviderID());
        $this->assertEquals('testName', $entry->getName());
        $this->assertEquals('testLoc', $entry->getLoc());

        $this->assertEquals($this->document->saveXML($this->document->documentElement), strval($entry));
    }

    /**
     */
    public function testMarshallingNullables(): void
    {
        $document = $this->document;
        $document->documentElement->removeAttribute('Name');
        $document->documentElement->removeAttribute('Loc');

        $entry = new IDPEntry('urn:some:requester', null, null);

        $this->assertEquals('urn:some:requester', $entry->getProviderID());
        $this->assertNull($entry->getName());
        $this->assertNull($entry->getLoc());

        $this->assertEquals(
            $this->document->saveXML($document->documentElement),
            strval($entry)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $entry = IDPEntry::fromXML($this->document->documentElement);

        $this->assertEquals('urn:some:requester', $entry->getProviderID());
        $this->assertEquals('testName', $entry->getName());
        $this->assertEquals('testLoc', $entry->getLoc());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(IDPEntry::fromXML($this->document->documentElement))))
        );
    }
}
