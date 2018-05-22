<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 20/10/17
 * Time: 11:12
 */

namespace TBoileau\ResourceBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute("dir", $options["dir"]);
        $builder->setAttribute("types", $options["types"]);
        $builder->setAttribute("ratio", $options["ratio"]);
        $builder->setAttribute("minWidth", $options["minWidth"]);
        $builder->setAttribute("maxWidth", $options["maxWidth"]);
        $builder->setAttribute("minHeight", $options["minHeight"]);
        $builder->setAttribute("maxHeight", $options["maxHeight"]);
        $builder->setAttribute("maxSize", $options["maxSize"]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars["dir"] = $options["dir"];
        $view->vars["label"] = $options["label"];
        $view->vars["types"] = $options["types"];
        $view->vars["ratio"] = $options["ratio"];
        $view->vars["minWidth"] = $options["minWidth"];
        $view->vars["maxWidth"] = $options["maxWidth"];
        $view->vars["minHeight"] = $options["minHeight"];
        $view->vars["maxHeight"] = $options["maxHeight"];
        $view->vars["maxSize"] = $options["maxSize"];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            "types",
            "dir"
        ]);
        $resolver->setDefaults([
            "ratio" => null,
            "minWidth" => null,
            "maxWidth" => null,
            "minHeight" => null,
            "maxHeight" => null,
            "maxSize" => null
        ]);
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
