#!/usr/bin/env php
<?php

require_once(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

use SimpleSAML\SAML2\Constants;
use SimpleSAML\SAML2\XML\mdattr\EntityAttributes;
use SimpleSAML\SAML2\XML\saml\Attribute;
use SimpleSAML\SAML2\XML\saml\AttributeValue;
use SimpleSAML\SAML2\XML\saml\Assertion;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

$attribute1 = new Attribute(
    'attrib1',
    Constants::NAMEFORMAT_URI,
    null,
    [
        new AttributeValue('is'),
        new AttributeValue('really'),
        new AttributeValue('cool'),
    ]
);

$attribute2 = new Attribute(
    'foo',
    'urn:simplesamlphp:v1:simplesamlphp',
    null,
    [
        new AttributeValue('is'),
        new AttributeValue('really'),
        new AttributeValue('cool')
    ]
);

$assertion = DOMDocumentFactory::fromFile('../tests/resources/xml/assertions/unsignedassertion_only_attributestatement.xml');
$unsignedAssertion = Assertion::fromXML($assertion->documentElement);

$privateKey = PEMCertificatesMock::getPrivateKey(XMLSecurityKey::RSA_SHA256, PEMCertificatesMock::SELFSIGNED_PRIVATE_KEY);
$unsignedAssertion->setSigningKey($privateKey);
$unsignedAssertion->setCertificates([PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::SELFSIGNED_PUBLIC_KEY)]);

$signedAssertion = $unsignedAssertion->toXML();
echo 'PRE:  ' . $signedAssertion->ownerDocument->saveXML();

$ea = new EntityAttributes([$attribute1, Assertion::fromXML($signedAssertion), $attribute2]);
echo 'POST:  ' . strval($ea->toXML()->ownerDocument->saveXML());
