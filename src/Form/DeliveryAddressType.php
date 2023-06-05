<?php declare(strict_types=1);

namespace App\Form;

use App\Domain\DeliveryAddressServiceInterface;
use App\Entity\DeliveryAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryAddressType extends AbstractType
{
    public function __construct(private readonly DeliveryAddressServiceInterface $deliveryService)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => DeliveryAddress::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('phone', TextType::class, [
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'required' => false
            ])
            ->add('country', ChoiceType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'choices' => $this->deliveryService->getCountries(),
            ]);

        $this->addRequiredTaxNumberField($builder);
        $builder->add('submit', SubmitType::class);

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (isset($data['country']) && $this->deliveryService->isEuropeanUnionMember($data['country'])) {
            $this->addRequiredTaxNumberField($form);
        } else {
            $this->addOptionalTaxNumberField($form);
        }
    }

    private function addOptionalTaxNumberField(FormInterface|FormBuilderInterface $form): void
    {
        $form->add('tax_number', TextType::class, [
            'required' => false,
        ]);
    }

    private function addRequiredTaxNumberField(FormInterface|FormBuilderInterface $form): void
    {
        $form->add('tax_number', TextType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
            'help' => 'Please enter your EU VAT ID',
        ]);
    }
}
