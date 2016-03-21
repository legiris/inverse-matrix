<?php

namespace App\Presenters;

use Nette;
use App\Model;


class HomepagePresenter extends BasePresenter
{
  /** @var \App\Model\Matrix */
  private $matrix;
  
  /** @var array */
  private $inverseMatrix;
  
  /** @var array */
  private $userMatrix;
  

  public function __construct(\App\Model\Matrix $matrix)
  {
    parent::__construct();
    $this->matrix = $matrix;
  }
  
  
	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
    $this->template->userMatrix = $this->userMatrix;
    $this->template->inverseMatrix = $this->inverseMatrix;
	}


	public function createComponentMatrixForm()
	{
		$dimensions = array(
			2 => '2 x 2',
			3 => '3 x 3',
			4 => '4 x 4',
			5 => '5 x 5',
			6 => '6 x 6',
			7 => '7 x 7',
			8 => '8 x 8',
			9 => '9 x 9',
			10 => '10 x 10',
		);

		$form = new Nette\Application\UI\Form();
		$form->addSelect('dimension', 'Vyberte rozměr matice:', $dimensions)
			->setDefaultValue(3)
			->setHtmlId('dimension');
		$form->addSubmit('send', 'Inverzní matice');
    
    $form->onSuccess[] = array($this, 'handleMatrixForm');
		return $form;
	}

  
  public function handleMatrixForm(\Nette\Application\UI\Form $form)
  {
    $post = $this->getHttpRequest()->getPost();
    
    $this->matrix->init($post['matrix']);
    $this->userMatrix = $this->matrix->getUserMatrix();
    $this->inverseMatrix = $this->matrix->getInverseMatrix();
  }
  

}
