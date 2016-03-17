<?php
namespace Doctrineum\Tests\Integer;

use Doctrineum\Scalar\Enum;
use Granam\Tests\Exceptions\Tools\AbstractExceptionsHierarchyTest;

class ExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace()
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace()
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    protected function getExternalRootNamespaces()
    {
        $externalRootReflection = new \ReflectionClass(Enum::class);

        return [$externalRootReflection->getNamespaceName()];
    }

}