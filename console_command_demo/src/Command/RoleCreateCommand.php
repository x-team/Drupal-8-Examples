<?php

namespace Drupal\console_command_demo\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Drupal\Console\Core\Command\Shared\ContainerAwareCommandTrait;
use Drupal\Console\Core\Style\DrupalStyle;
use Symfony\Component\Console\Input\InputOption;
use Drupal\user\Entity\Role;

/**
 * Class RoleCreateCommand.
 *
 * @package Drupal\console_command_demo
 *
 * @DrupalCommand (
 *     extension="console_command_demo",
 *     extensionType="module"
 * )
 */
class RoleCreateCommand extends Command {

  use ContainerAwareCommandTrait;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('user:role:create')
      ->setDescription($this->trans('commands.user.role.create.description'))
      ->addArgument(
        'label',
        InputOption::VALUE_REQUIRED,
        $this->trans('commands.user.role.create.options.label')
      )
      ->addOption(
        'id',
        NULL,
        InputOption::VALUE_OPTIONAL,
        $this->trans('commands.user.role.create.options.id')
      )
      ->addOption(
        'weight',
        NULL,
        InputOption::VALUE_OPTIONAL,
        $this->trans('commands.user.role.create.options.weight'),
        0
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    $label = $input->getArgument('label');
    while (!$label) {
      $label = $io->askEmpty(
        $this->trans('commands.user.role.create.questions.label'),
        NULL
      );
    }
    $input->setArgument('label', $label);

    $id = $input->getOption('id');
    if (!$id) {
      $id = $io->askEmpty(
        $this->trans('commands.user.role.create.questions.id'),
        NULL
      );
    }
    $input->setOption('id', $id);

    $weight = $input->getOption('weight');
    if (!$weight) {
      $weight = $io->ask(
        $this->trans('commands.user.role.create.questions.weight'),
        0,
        NULL
      );
    }
    $input->setOption('weight', $weight);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    $id = $input->getOption('id');
    $label = $input->getArgument('label');
    $weight = $input->getOption('weight');

    $role = $this->createRole($label, $id, $weight);

    $tableHeader = ['Field', 'Value'];

    $tableFields = [
      $this->trans('commands.user.role.create.messages.id'),
      $this->trans('commands.user.role.create.messages.label'),
      $this->trans('commands.user.role.create.messages.weight'),
    ];

    if ($role['success']) {
      $tableData = array_map(
        function ($field, $value) {
          return [$field, $value];
        },
        $tableFields,
        $role['success']
      );

      $io->table($tableHeader, $tableData);
      $io->success(sprintf($this->trans('commands.user.role.create.messages.success'), $role['success']['label']));

      return 0;
    }

    if ($role['error']) {
      $io->error($role['error']['error']);
    }

  }

  /**
   * Create a user role.
   *
   * @param string $label
   *   The name of the role.
   * @param string $id
   *   The machine name  of the role/.
   * @param int $weight
   *   The weight.
   *
   * @return array
   *   Array of data depending on success or failure.
   */
  private function createRole($label, $id, $weight) {
    $role = Role::create(
      [
        'label' => $label,
        'id' => str_replace(' ', '_', strtolower($id ?: $label)),
        'weight' => $weight,
      ]
    );

    $result = [];

    try {
      $role->save();

      $result['success'] = [
        'id' => $role->id(),
        'label' => $role->label(),
        'weight' => $role->getWeight(),
      ];
    }
    catch (\Exception $e) {
      $result['error'] = [
        'id' => $role->id(),
        'label' => $role->get('label'),
        'error' => 'Error: ' . get_class($e) . ', code: ' . $e->getCode() . ', message: ' . $e->getMessage(),
      ];
    }

    return $result;
  }

}
