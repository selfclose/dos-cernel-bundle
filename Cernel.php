<?php

namespace DoS\CernelBundle;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class Cernel extends Kernel
{
    const VERSION         = '0.15.0-dev';
    const VERSION_ID      = '00150';
    const MAJOR_VERSION   = '0';
    const MINOR_VERSION   = '15';
    const RELEASE_VERSION = '0';
    const EXTRA_VERSION   = 'DEV';

    const ENV_DEV = 'dev';
    const ENV_PROD = 'prod';
    const ENV_TEST = 'test';
    const ENV_STAGING = 'staging';

    public function registerBundles()
    {
        $bundles = array(
            new \FM\ElfinderBundle\FMElfinderBundle(),
            new \Uecode\Bundle\QPushBundle\UecodeQPushBundle(),
            new \Cocur\Slugify\Bridge\Symfony\CocurSlugifyBundle(),

            new \Sonata\SeoBundle\SonataSeoBundle(),
            new \Sonata\BlockBundle\SonataBlockBundle(),

            new \Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new \Symfony\Cmf\Bundle\BlockBundle\CmfBlockBundle(),
            new \Symfony\Cmf\Bundle\ContentBundle\CmfContentBundle(),
            new \Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new \Symfony\Cmf\Bundle\MenuBundle\CmfMenuBundle(),
            new \Symfony\Cmf\Bundle\CreateBundle\CmfCreateBundle(),
            new \Symfony\Cmf\Bundle\MediaBundle\CmfMediaBundle(),

            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new \Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

            new \DoS\QueueBundle\DoSQueueBundle(),
            new \DoS\CernelBundle\DoSCernelBundle(),
            new \DoS\ResourceBundle\DoSResourceBundle(),
            new \DoS\SettingsBundle\DoSSettingsBundle(),
            new \DoS\UserBundle\DoSUserBundle(),
            new \DoS\MailerBundle\DoSMailerBundle(),
            new \DoS\SMSBundle\DoSSMSBundle(),
            new \Misd\PhoneNumberBundle\MisdPhoneNumberBundle(),

            new \Sylius\Bundle\MoneyBundle\SyliusMoneyBundle(),
            new \Sylius\Bundle\ContentBundle\SyliusContentBundle(),
            new \Sylius\Bundle\MediaBundle\SyliusMediaBundle(),
            new \Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new \Sylius\Bundle\SettingsBundle\SyliusSettingsBundle(),
            new \Sylius\Bundle\MailerBundle\SyliusMailerBundle(),
            new \Sylius\Bundle\RbacBundle\SyliusRbacBundle(),
            new \Sylius\Bundle\UserBundle\SyliusUserBundle(),
            new \Sylius\Bundle\ApiBundle\SyliusApiBundle(),
            new \Sylius\Bundle\ThemeBundle\SyliusThemeBundle(),

            new \Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new \FOS\RestBundle\FOSRestBundle(),

            new \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),

            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new \Oneup\AclBundle\OneupAclBundle(),

            //new \Helthe\Bundle\TurbolinksBundle\HeltheTurbolinksBundle(),
            new \Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),

            new \winzou\Bundle\StateMachineBundle\winzouStateMachineBundle(),

            new \CacheTool\Bundle\CacheToolBundle(),
            new \Nelmio\CorsBundle\NelmioCorsBundle(),
            #new \YZ\SupervisorBundle\YZSupervisorBundle(),
            new \BCC\CronManagerBundle\BCCCronManagerBundle(),
            new \SunCat\MobileDetectBundle\MobileDetectBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new \Elao\WebProfilerExtraBundle\WebProfilerExtraBundle();
            $bundles[] = new \Hautelook\AliceBundle\HautelookAliceBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $rootDir = $this->getRootDir();

        $loader->load($rootDir.'/config/config_'.$this->environment.'.yml');

        if (is_file($file = $rootDir.'/config/config_'.$this->environment.'.local.yml')) {
            $loader->load($file);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/dos/cache/'.$this->environment;
        }

        return parent::getCacheDir();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/dos/logs';
        }

        return parent::getLogDir();
    }

    /**
     * @return bool
     */
    protected function isVagrantEnvironment()
    {
        return (getenv('HOME') === '/home/vagrant' || getenv('VAGRANT') === 'VAGRANT') && is_dir('/dev/shm');
    }
}
