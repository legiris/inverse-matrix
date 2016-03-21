<?php

namespace App\Model;

/**
 * trida pro vypocet inverzni matice
 * $i - radek
 * $j - sloupec
 */
class Matrix extends \Nette\Object
{
  /**
   * rozmer matice
   * @var int
   */
  private $dimension;
  
  /**
   * zadana matice, ponechame beze zmeny
   * @var array
   */
  private $matrix;

  /**
   * matice na leve strane
   * @var array
   */
  private $leftMatrix;
  
  /**
   * matice na prave strane
   * @var array
   */
  private $rightMatrix;
  
  /**
   * zda muze byt inverzni matice
   * pokud je determinant matice = 0, tak je matice singularni a neexistuje k ni inverzni matice
   * spravne by se mela vypocitat hodnota determinantu, jinak pri vypoctu dojde k deleni nulou...
   * @var boolean
   */
  private $isInverseMatrix = TRUE;
  

  /**
   * inicializace
   * @param array $matrix
   */
  public function init($matrix)
  {
    $this->dimension = count($matrix);
    $this->matrix = $matrix;
    $this->setInverseMatrix($this->dimension);
    $this->leftMatrix = $this->matrix;
    $this->loader();
  }
  
  
  private function loader()
  {
    while (!$this->isIdentityMatrix($this->leftMatrix) && $this->isInverseMatrix) {
      $this->countMatrix();
    }
  }

  
  /**
   * vypocet matice
   */
  private function countMatrix()
  {
    for ($i = 1; $i <= count($this->leftMatrix); $i++) {
      for ($j = 1; $j <= count($this->leftMatrix); $j++) {
          
        if ($i == $j AND $this->leftMatrix[$i][$j] == 1) {
          // hodnota v diagonale = 1, radek je ok a muzeme prejit na upravu hodnot ve sloupci
          $this->loopRows($i, $j);
        } elseif ($i == $j AND $this->leftMatrix[$i][$j] != 1) {
          // hodnota v diagonale != 1, musime prepocitat radek
          try {
            $this->divideRow($i, $this->leftMatrix[$i][$j]);
          } catch (\Exception $e) {
            $this->isInverseMatrix = FALSE;
          }
        } else {
          // hodnota mimo diagonalu
        }
      }
    }
  }
  
  
  /**
   * deleni radku matice zadanou hodnotou
   * @param int $rowId
   * @param int $value
   * @throws \Exception
   */
  private function divideRow($rowId, $value)
  {
    if ($value == 0) {
      throw new \Exception('Division by zero');
    }
    
    for ($col = 1; $col <= $this->dimension; $col++) {
      $this->leftMatrix[$rowId][$col] = round($this->leftMatrix[$rowId][$col]/$value, 8);
      $this->rightMatrix[$rowId][$col] = round($this->rightMatrix[$rowId][$col]/$value, 8);
    }
  }
  
  
  /**
   * projde vsechny radky v danem sloupci a prepocita je
   * @param int $i
   * @param int $j
   */
  private function loopRows($i, $j)
  {
    for ($row = 1; $row <= $this->dimension; $row++) {
      if ($row != $i) { // vynechame vychozi radek
        $value = $this->leftMatrix[$row][$j] * (-1);
        $this->countRow($i, $row, $value);
      }
    }
  }

  
  /**
   * vynasobi radek $rowId zadanou hodnotou a pricte k dalsimu radku
   * rowId je vychozi radek, ktery se ma nasobit hodnotou
   * addRowId je radek, ke kteremu se pricita
   * @param int $rowId
   * @param int $addRowId
   * @param int $value
   */
  private function countRow($rowId, $addRowId, $value)
  {
    $defaultRowLeftMatrix = $this->leftMatrix[$rowId];
    $defaultRowRightMatrix = $this->rightMatrix[$rowId];
    
    for ($col = 1; $col <= $this->dimension; $col++) {
      $this->leftMatrix[$addRowId][$col] = $value * $defaultRowLeftMatrix[$col] + $this->leftMatrix[$addRowId][$col];
    }

    for ($col = 1; $col <= $this->dimension; $col++) {
      $this->rightMatrix[$addRowId][$col] = $value * $defaultRowRightMatrix[$col] + $this->rightMatrix[$addRowId][$col];
    }
  }
  
  
  /**
   * kontrola, zda je matice jednotkova
   * @param array $matrix
   * @return boolean
   */
  private function isIdentityMatrix($matrix)
  {
    for ($i = 1; $i <= $this->dimension; $i++) {
      for ($j = 1; $j <= $this->dimension; $j++) {
        if ($i == $j) {
          // hodnoty v diagonale
          if ($matrix[$i][$j] != 1) {
            return FALSE;
          }
        } else {
          if ($matrix[$i][$j] != 0) {
            return FALSE;
          }
        }
      }
    }
    
    return TRUE;
  }
  
  
  /**
   * podle rozmeru matice nastavi matici na prave strane na jednotkovou matici
   * @param int $dimension
   */
  private function setInverseMatrix($dimension)
  {
    for ($i = 1; $i <= $dimension; $i++) {
      for ($j = 1; $j <= $dimension; $j++) {
        if ($i == $j) {
          $this->rightMatrix[$i][$j] = 1;
        } else {
          $this->rightMatrix[$i][$j] = 0;
        }
      }
    }
  }


  /**
   * @return int
   */
  public function getDimension()
  {
    return $this->dimension;
  }

  /**
   * vrati zadanou matici
   * @return array
   */
  public function getUserMatrix()
  {
    return $this->matrix;
  }

  /**
   * vrati inverzni matici
   * @return array|false
   */
  public function getInverseMatrix()
  {
    if ($this->isInverseMatrix === TRUE) {
      return $this->rightMatrix;
    } else {
      return FALSE;
    }      
  }
  
}
