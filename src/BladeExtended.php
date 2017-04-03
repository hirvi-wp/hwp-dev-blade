<?php

namespace HWP\Blade;

class BladeExtended extends Blade
{

    /**
     * BladeExtended constructor.
     *
     * @param array $viewPaths
     * @param string $cachePath
     * @param Dispatcher|null $events
     */
    public function __construct($viewPaths = array(), $cachePath, Dispatcher $events = null)
    {
        parent::__construct($viewPaths, $cachePath, $events);

        $this->extend();
    }

    /**
     *
     */
    private function extend()
    {
        // add @wpposts
        $this->getCompiler()->extend(function($view, $compiler)
        {
            return str_replace('@wpposts', '<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>', $view);
        });

        // add @wpquery
        $this->getCompiler()->extend(function($view, $compiler)
        {
            $pattern = '/(\s*)@wpquery(\s*\(.*\))/';
            $replacement  = '$1<?php $bladequery = new WP_Query$2; ';
            $replacement .= 'if ( $bladequery->have_posts() ) : ';
            $replacement .= 'while ( $bladequery->have_posts() ) : ';
            $replacement .= '$bladequery->the_post(); ?> ';
            return preg_replace( $pattern, $replacement, $view );
        });

        // add @wpempty
        $this->getCompiler()->extend(function($view, $compiler)
        {
            return str_replace('@wpempty', '<?php endwhile; ?><?php else: ?>', $view);
        });

        // add @wpend
        $this->getCompiler()->extend(function($view, $compiler)
        {
            return str_replace('@wpend', '<?php endif; wp_reset_postdata(); ?>', $view);
        });
    }

}