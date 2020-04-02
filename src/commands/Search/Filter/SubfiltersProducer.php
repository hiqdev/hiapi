<?php

namespace hiapi\commands\Search\Filter;

use hiapi\commands\Search\Filter\Type\ArrayOfFilter;
use hiapi\commands\Search\Filter\Type\FilterInterface;
use hiapi\commands\Search\Filter\Type\GenericFilter;
use hiapi\commands\Search\Filter\Type\IntegerFilter;

class SubfiltersProducer
{
    /**
     * @psalm-var array<class-string<FilterInterface>, array<string, class-string<FilterInterface>|list<class-string<FilterInterface>>>>
     */
    private $map = [
        // Either this way
        IntegerFilter::class => [
            '_ne' => IntegerFilter::class,
            '_gt' => IntegerFilter::class,
            '_lt' => IntegerFilter::class,
            '_in' => [ArrayOfFilter::class, IntegerFilter::class],
            '_ni' => [ArrayOfFilter::class, IntegerFilter::class],
        ]
    ];

    /**
     * @psalm-var array<
        string,
        array<string, string|array{class-string<FilterInterface>, string}>
      >
     */
    private $constantsMap = [
        // OR this way.
        // Cons:
        //  - no real namespaces for constant values
        //  - filter name should be passed nearby as a separate property
        GenericFilter::INTEGER => [
            '_ne' => GenericFilter::INTEGER,
            '_gt' => GenericFilter::INTEGER,
            '_lt' => GenericFilter::INTEGER,
            '_in' => [ArrayOfFilter::class, GenericFilter::INTEGER],
            '_ni' => [ArrayOfFilter::class, GenericFilter::INTEGER],
        ]
    ];

    /**
     * @param FilterInterface $filter
     * @return FilterInterface[]
     * @psalm-return list<FilterInterface>
     */
    public function produce(FilterInterface $filter): array
    {
        return $filter instanceof GenericFilter
            ? $this->produceAsGeneric($filter)
            : $this->produceAsSeparateClass($filter);
    }

    private function produceAsGeneric(FilterInterface $filter): array
    {
        $type = $filter->type();
        if (!isset($this->constantsMap[$type])) {
            return [];
        }

        $result = [];
        foreach ($this->constantsMap[$type] as $suffix => $newType) {
            $attributeName = $filter->name() . $suffix;

            if (is_array($newType)) {
                $className = array_shift($newType);
                $result[] = new $className($attributeName, $newType[0]);
            } else {
                $result[] = GenericFilter::__callStatic($newType, [$attributeName]);
            }
        }

        return $result;
    }

    private function produceAsSeparateClass(FilterInterface $filter): array
    {
        $type = get_class($filter);
        if (!isset($this->map[$type])) {
            return [];
        }

        $result = [];
        foreach ($this->map[$type] as $suffix => $newType) {
            $attributeName = $filter->name() . $suffix;

            if (is_array($newType)) {
                $className = array_shift($newType);
                $result[] = new $className($attributeName, $newType[0]);
            } else {
                $result[] = new $newType($attributeName);
            }
        }

        return $result;
    }
}
