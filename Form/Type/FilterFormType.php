<?php

namespace FlexModel\FlexModelElasticsearchBundle\Form\Type;

use FlexModel\FlexModel;
use ReflectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FilterFormType.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class FilterFormType extends AbstractType
{
    /**
     * The FlexModel instance.
     *
     * @var FlexModel
     */
    private $flexModel;

    /**
     * Constructs a new FlexModelFormType instance.
     *
     * @param FlexModel $flexModel
     */
    public function __construct(FlexModel $flexModel)
    {
        $this->flexModel = $flexModel;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['data_class']) && isset($options['form_name'])) {
            $reflectionClass = new ReflectionClass($options['data_class']);
            $objectName = $reflectionClass->getShortName();

            $formConfiguration = $this->flexModel->getFormConfiguration($objectName, $options['form_name']);
            if (is_array($formConfiguration)) {
                foreach ($formConfiguration['fields'] as $formFieldConfiguration) {
                    $fieldConfiguration = $this->flexModel->getField($objectName, $formFieldConfiguration['name']);

                    $fieldType = $this->getFieldType($formFieldConfiguration, $fieldConfiguration);
                    $fieldOptions = $this->getFieldOptions($formConfiguration, $fieldConfiguration);

                    if (isset($options['aggregation_results'][$formFieldConfiguration['name']])) {
                        $fieldOptions['choices'] = $options['aggregation_results'][$formFieldConfiguration['name']];
                        $fieldOptions['choice_label'] = function($aggregationResult) {
                            return $aggregationResult->getLabel();
                        };
                        $fieldOptions['choice_value'] = function($aggregationResult) {
                            return $aggregationResult->getValue();
                        };
                        $fieldOptions['choice_attr'] = function($aggregationResult) {
                            return array(
                                'disabled' => $aggregationResult->getCount() < 1,
                                'data-count' => $aggregationResult->getCount(),
                            );
                        };
                    }

                    $builder->add($fieldConfiguration['name'], $fieldType, $fieldOptions);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('validation_groups', false);
        $resolver->setDefault('form_name', null);
        $resolver->setDefault('aggregation_results', array());
    }

    /**
     * Returns the field type.
     *
     * @param array $formFieldConfiguration
     * @param array $fieldConfiguration
     *
     * @return string
     */
    protected function getFieldType(array $formFieldConfiguration, array $fieldConfiguration)
    {
        return ChoiceType::class;
    }

    /**
     * Returns the field options.
     *
     * @param array $formFieldConfiguration
     * @param array $fieldConfiguration
     *
     * @return array
     */
    protected function getFieldOptions(array $formFieldConfiguration, array $fieldConfiguration)
    {
        $label = $fieldConfiguration['label'];
        if (isset($formFieldConfiguration['label'])) {
            $label = $formFieldConfiguration['label'];
        }

        return array(
            'label' => $label,
            'required' => false,
            'multiple' => true,
            'expanded' => true,
        );
    }
}
