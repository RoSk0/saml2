<?php

declare(strict_types=1);

namespace SimpleSAML\SAML2\Compat\Ssp;

use Psr\Log\LoggerInterface;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML2\Compat\ContainerInterface;
use SimpleSAML\SAML2\XML\saml\CustomIdentifierInterface;
use SimpleSAML\XML\AbstractXMLElement;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\Utils\Random;
use SimpleSAML\Utils\System;
use SimpleSAML\Utils\XML;

class Container implements ContainerInterface
{
    /** @var \Psr\Log\LoggerInterface */
    protected LoggerInterface $logger;

    /** @var array */
    protected array $registry = [];


    /**
     * Create a new SimpleSAMLphp compatible container.
     */
    public function __construct()
    {
        $this->logger = new Logger();
    }


    /**
     * {@inheritdoc}
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }


    /**
     * {@inheritdoc}
     * @return string
     */
    public function generateId(): string
    {
        /** @psalm-suppress UndefinedClass */
        return Random::generateID();
    }


    /**
     * {@inheritdoc}
     * @param mixed $message
     * @param string $type
     */
    public function debugMessage($message, string $type): void
    {
        /** @psalm-suppress UndefinedClass */
        XML::debugSAMLMessage($message, $type);
    }


    /**
     * {@inheritdoc}
     * @param string $url
     * @param array $data
     */
    public function redirect(string $url, array $data = []): void
    {
        /** @psalm-suppress UndefinedClass */
        HTTP::redirectTrustedURL($url, $data);
    }


    /**
     * {@inheritdoc}
     * @param string $url
     * @param array $data
     */
    public function postRedirect(string $url, array $data = []): void
    {
        /** @psalm-suppress UndefinedClass */
        HTTP::submitPOSTData($url, $data);
    }


    /**
     * {@inheritdoc}
     * @return string
     */
    public function getTempDir(): string
    {
        /** @psalm-suppress UndefinedClass */
        return System::getTempDir();
    }


    /**
     * {@inheritdoc}
     * @param string $filename
     * @param string $date
     * @param int|null $mode
     */
    public function writeFile(string $filename, string $data, int $mode = null): void
    {
        if ($mode === null) {
            $mode = 0600;
        }
        /** @psalm-suppress UndefinedClass */
        System::writeFile($filename, $data, $mode);
    }


    /**
     * @inheritDoc
     */
    public function registerExtensionHandler(string $class): void
    {
        Assert::subclassOf($class, AbstractXMLElement::class);

        if (is_subclass_of($class, CustomIdentifierInterface::class, true)) {
            $key = $class::getXsiType() . ':BaseID';
        } else {
            $key = join(':', [urlencode($class::NS), AbstractXMLElement::getClassName($class)]);
        }
        $this->registry[$key] = $class;
    }


    /**
     * @inheritDoc
     */
    public function getElementHandler(string $namespace, string $element): ?string
    {
        Assert::notEmpty($namespace, 'Cannot search for handlers without an associated namespace URI.');
        Assert::notEmpty($element, 'Cannot search for handlers without an associated element name.');

        return $this->registry[join(':', [urlencode($namespace), $element])];
    }


    /**
     * @inheritDoc
     */
    public function getIdentifierHandler(string $type): ?string
    {
        Assert::notEmpty($type, 'Cannot search for identifier handlers with an empty type.');

        $handler = $type . ':BaseID';
        return array_key_exists($handler, $this->registry) ? $this->registry[$handler] : null;
    }
}
