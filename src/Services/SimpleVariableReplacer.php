<?php

namespace Davesweb\Repositories\Services;

class SimpleVariableReplacer
{
    /**
     * @param string $input
     * @param array  $variables
     *
     * @return string
     */
    public function replace($input, array $variables)
    {
        $names  = $this->getVariableNamesToReplace($variables);
        $values = $this->getValuesForReplace($variables);

        return str_replace($names, $values, $input);
    }

    /**
     * Returns the names of the variables to replace, including the { and }.
     *
     * @param array $variables
     *
     * @return array
     */
    private function getVariableNamesToReplace(array $variables)
    {
        return array_map(function ($variable) {
            return '{' . $variable . '}';
        }, array_keys($variables));
    }

    /**
     * Returns the values for the variables that need to be replaced.
     *
     * @param array $variables
     *
     * @return array
     */
    private function getValuesForReplace(array $variables)
    {
        return array_values($variables);
    }
}
