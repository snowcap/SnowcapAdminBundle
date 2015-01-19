Custom Form types
=================

TextAutocomplete
----------------

JavaScript plugin Typeahead is included in SnowcapAdminBundle. Therefore you can create an autocomplete that returns only text by passing an extra parameter to text type named *list_url*.
This url must have a GET parameter having *__query__* value by default, so that Typeahead can replace *__query__* with the real text query.

.. code-block:: php

    <?php

    namespace Your\AdminBundle\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    class MyFormType extends AbstractType
    {
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('name', 'text', array(
                'label' => 'my_form.name',
                // Get router using service injection
                'list_url' => $this->router->generate('your_admin_my_form_filter_autocomplete', array('q' => '__query__'))
            ));
        }
    }

The called action must send a JsonResponse containing an array of results:

.. code-block:: php

    <?php

    class MyController extends Controller
    {
        public function filterAutocompleteAction(Request $request)
        {
            // Retrieve query from "q" GET param
            $q = $request->query->get('q');

            /** @var \Doctrine\ORM\EntityRepository $repo */
            $repo = $this->getDoctrine()->getManager()
                ->getRepository('YourBundle:Entity');
            $data = $repo->createQueryBuilder('e')
                ->select('e.name')
                ->where('e.name LIKE :query')
                ->setParameter('query', '%' . $q . '%')
                ->orderBy('e.name', 'ASC')
                ->setMaxResults(10)
                ->getQuery()->getArrayResult();

            $results = array();
            foreach ($data as $item) {
                $results[] = $item['name'];
            }

            return new JsonResponse(array('result' => $results));
        }
    }

Autocomplete (entities)
-----------------------

There's also an Autocomplete type that works with entities. For that you will need an Admin class to work with.

Options
~~~~~~~

====================    ========    ============    ========    ================================
Name                    Type        Default         Required    Description
====================    ========    ============    ========    ================================
admin                   string      null            Yes         Admin class alias
where                   string      null            Yes         Query to match the related entities
allow_add               boolean     false           No          Allow adding a new related entity via the current form
multiple                boolean     false           No          If it is a OneToMany or ManyToMany relation
id_property             string      "id"            No          Identifier of the related entity
property                string      "__toString"    No          Text identifying the related entity (eg. its name)
====================    ========    ============    ========    ================================

Example
~~~~~~~

.. code-block:: php

    <?php

    namespace Your\AdminBundle\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    class MyFormType extends AbstractType
    {
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('tag', 'snowcap_admin_autocomplete', array(
                'label'    => 'my_form.tag',
                'admin'    => 'tag',
                'where'    => 'e.name LIKE :query',
                'property' => 'name'
            ));
        }
    }
