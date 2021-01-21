<?php

declare(strict_types=1);

use EMO\PaymentStatementParserBundle\CompilerPass\PublicForTestsCompilerPass;
use EMO\PaymentStatementParserBundle\EMOPaymentStatementParserBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * App kernel used for testing bundle without installing bundle to the real app.
 */
class AppKernel extends Kernel
{
    /**
     * @return string[]
     */
    public function registerBundles(): array
    {
        $bundles = [];
        if ($this->getEnvironment() === 'test') {
            $bundles[] = new FrameworkBundle();
            $bundles[] = new EMOPaymentStatementParserBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config.yaml');
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new PublicForTestsCompilerPass());
    }
}
