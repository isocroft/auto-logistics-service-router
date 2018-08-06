<?php
      return Symfony\CS\Config::create()
        ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
        ->fixers([
          'php_closing_tag',
          'ordered_use',
          'extra_empty_lines',
          'eof_ending',
          'indentation',
          'trailing_spaces',
          'lowercase_keywords'
        ]);
?>
