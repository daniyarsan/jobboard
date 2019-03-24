<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\TwigFunction;

class HelperExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('query_string', array($this, 'getQueryString')),
        );
    }

    public function getFilters()
    {
        return array(
//            new TwigFilter('format_currency', array($this, 'formatCurrency')),
        );
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        $args = func_get_args();
        $request = Request::createFromGlobals();
        foreach ($args as $arg) {
            $request->query->set(key($arg), $arg[key($arg)]);
        }

        return '?' . http_build_query($request->query->all());
    }

    
    public function getName()
    {
        return 'twig_helper';
    }
}
