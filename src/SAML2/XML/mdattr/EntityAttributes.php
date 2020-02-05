<?php

declare(strict_types=1);

namespace SimpleSAML\SAML2\XML\mdattr;

use DOMElement;
<<<<<<< HEAD
=======
use SAML2\Exception\InvalidDOMElementException;
use SAML2\Utils;
use SAML2\XML\Assertion;
use SAML2\XML\saml\Attribute;
>>>>>>> Chunk > Assertion
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML2\XML\saml\Attribute;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Utils as XMLUtils;

/**
 * Class for handling the EntityAttributes metadata extension.
 *
 * @link: http://docs.oasis-open.org/security/saml/Post2.0/sstc-metadata-attr-cs-01.pdf
 * @package simplesamlphp/saml2
 */
final class EntityAttributes extends AbstractMdattrElement
{
    /**
     * Array with child elements.
     *
<<<<<<< HEAD
     * The elements can be \SimpleSAML\SAML2\XML\saml\Attribute or \SimpleSAML\XML\Chunk elements.
     *
     * @var (\SimpleSAML\SAML2\XML\saml\Attribute|\SimpleSAML\XML\Chunk)[]
=======
     * The elements can be \SAML2\XML\saml\Attribute or \SAML2\XML\saml\Assertion elements.
     *
     * @var (\SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute)[]
>>>>>>> Chunk > Assertion
     */
    protected array $children = [];


    /**
     * Create a EntityAttributes element.
     *
<<<<<<< HEAD
     * @param (\SimpleSAML\XML\Chunk|\SimpleSAML\SAML2\XML\saml\Attribute)[] $children
=======
     * @param (\SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute)[] $children
>>>>>>> Chunk > Assertion
     */
    public function __construct(array $children)
    {
        $this->setChildren($children);
    }


    /**
     * Collect the value of the children-property
     *
<<<<<<< HEAD
     * @return (\SimpleSAML\XML\Chunk|\SimpleSAML\SAML2\XML\saml\Attribute)[]
=======
     * @return (\SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute)[]
>>>>>>> Chunk > Assertion
     */
    public function getChildren(): array
    {
        return $this->children;
    }


    /**
     * Set the value of the childen-property
     *
<<<<<<< HEAD
     * @param (\SimpleSAML\XML\Chunk|\SimpleSAML\SAML2\XML\saml\Attribute)[] $children
=======
     * @param (\SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute)[] $children
>>>>>>> Chunk > Assertion
     * @return void
     * @throws \SimpleSAML\Assert\AssertionFailedException
     */
    private function setChildren(array $children): void
    {
        Assert::allIsInstanceOfAny($children, [Assertion::class, Attribute::class]);

        $this->children = $children;
    }


    /**
     * Add the value to the children-property
     *
<<<<<<< HEAD
     * @param \SimpleSAML\XML\Chunk|\SimpleSAML\SAML2\XML\saml\Attribute $child
=======
     * @param \SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute $child
>>>>>>> Chunk > Assertion
     * @return void
     * @throws \SimpleSAML\Assert\AssertionFailedException
     */
    public function addChild($child): void
    {
        Assert::isInstanceOfAny($child, [Assertion::class, Attribute::class]);

        $this->children[] = $child;
    }


    /**
     * Convert XML into a EntityAttributes
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'EntityAttributes', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, EntityAttributes::NS, InvalidDOMElementException::class);

        $children = [];

        /** @var \DOMElement $node */
        foreach (XMLUtils::xpQuery($xml, './saml_assertion:Attribute|./saml_assertion:Assertion') as $node) {
            if ($node->localName === 'Attribute') {
                $children[] = Attribute::fromXML($node);
            } elseif ($node->localName === 'Assertion') {
                $children[] = new Assertion($node);
            } else {
                throw new \InvalidArgumentException('Illegal content in mdattr:EntityAttributes message.');
            }
        }

        return new self($children);
    }


    /**
     * Convert this EntityAttributes to XML.
     *
     * @param \DOMElement|null $parent The element we should append to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->children as $child) {
            $child->toXML($e);
        }

        return $e;
    }
}
