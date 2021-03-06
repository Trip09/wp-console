<?php

/**
 * @file
 * Contains WP\Console\Command\Shared\ServicesTrait.
 */

namespace WP\Console\Command\Shared;

trait ServicesTrait
{
    /**
     * @return mixed
     */
    public function servicesQuestion()
    {
        if ($this->getIo()->confirm(
            $this->trans('commands.common.questions.services.confirm'),
            false
        )
        ) {
            $service_collection = [];
            $this->getIo()->writeln($this->trans('commands.common.questions.services.message'));
            $services = $this->container->getServiceIds();
            while (true) {
                $service = $this->getIo()->choiceNoList(
                    $this->trans('commands.common.questions.services.name'),
                    $services,
                    null,
                    true
                );

                $service = trim($service);
                if (empty($service)) {
                    break;
                }

                array_push($service_collection, $service);
                $service_key = array_search($service, $services, true);

                if ($service_key >= 0) {
                    unset($services[$service_key]);
                }
            }

            return $service_collection;
        }
    }

    /**
     * @param array $services
     *
     * @return array
     */
    public function buildServices($services)
    {
        if (!empty($services)) {
            $buildServices = [];
            foreach ($services as $service) {
                $class = get_class($this->container->get($service));
                $shortClass = explode('\\', $class);
                $machineName = str_replace('.', '_', $service);
                $buildServices[$service] = [
                  'name' => $service,
                  'machine_name' => $machineName,
                  'camel_case_name' => $this->stringConverter->underscoreToCamelCase($machineName),
                  'class' => $class,
                  'short' => end($shortClass),
                ];
            }

            return $buildServices;
        }

        return [];
    }
}
