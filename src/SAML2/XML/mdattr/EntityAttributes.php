<?php

declare(strict_types=1);

namespace SAML2\XML\mdattr;

use DOMElement;
use SAML2\Exception\InvalidDOMElementException;
use SAML2\Utils;
use SAML2\XML\Assertion;
use SAML2\XML\saml\Attribute;
use SimpleSAML\Assert\Assert;

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
     * The elements can be \SAML2\XML\saml\Attribute or \SAML2\XML\saml\Assertion elements.
     *
     * @var (\SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute)[]
     */
    protected $children = [];


    /**
     * Create a EntityAttributes element.
     *
     * @param (\SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute)[] $children
     */
    public function __construct(array $children)
    {
        $this->setChildren($children);
    }


    /**
     * Collect the value of the children-property
     *
     * @return (\SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute)[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }


    /**
     * Set the value of the childen-property
     *
     * @param (\SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute)[] $children
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
     * @param \SAML2\XML\saml\Assertion|\SAML2\XML\saml\Attribute $child
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
     * @throws \SAML2\Exception\InvalidDOMElementException if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'EntityAttributes', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, EntityAttributes::NS, InvalidDOMElementException::class);

        $children = [];

        /** @var \DOMElement $node */
        foreach (Utils::xpQuery($xml, './saml_assertion:Attribute|./saml_assertion:Assertion') as $node) {
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
