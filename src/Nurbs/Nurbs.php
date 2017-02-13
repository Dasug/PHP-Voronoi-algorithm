<?php
namespace sroze\Nurbs;

use sroze\Nurbs\Surface\Surface_Abstract;

/**
 * Cette classe définie un nurbs, à savoir une surface en 3 dimentions.
 * 
 */ 
class Nurbs extends Surface_Abstract
{
	/**
	 * Méthodes de calcul du nurbs.
	 * 
	 * @var integer
	 */
	const CALCUL_CLASSIC = 1;
	const CALCUL_DELAUNAY = 2;
	
	/**
	 * Méthode de calcul.
	 * 
	 */
	protected $_calcul_method = self::CALCUL_CLASSIC;
	
	/**
	 * Surfaces de Delaunay.
	 * 
	 * @var array
	 */
	protected $_delaunay_surfaces;
	
	/**
	 * Créé un nurbs depuis un tableau de points.
	 * 
	 * @param array $points
	 * @return sroze\Nurbs\Surface\Surface_Abstract
	 */
	public static function fromPoints (array $points)
	{
		$nurbs = new self();
		
		// On ajoutes les points
		$nurbs->addPoints($points);
		
		return $nurbs;
	}
	
	/**
	 * Change la méthode de calcul du nurbs.
	 * 
	 */
	public function setCalculMethod ($method)
	{
		$this->_calcul_method = $method;
	}
	
	/**
	 * Récupère un point du Nurbs.
	 * 
	 * @param integer $x
	 * @param integer $y
	 * @param double  $radius
	 * @return sroze\Nurbs\Point
	 */
	public function getPoint ($x, $y, $radius = 1)
	{
		switch ($this->_calcul_method) {
		case self::CALCUL_DELAUNAY:
			return $this->getDelaunayPoint($x, $y);
			break;
		default:
			return parent::getPoint($x, $y, $radius);
			break;
		}
	}
	
	/**
	 * Calcul un point grâce à la méthode de Delaunay, à savoir: on divise
	 * la surface en autres surfaces triangulaires plus simples à calculer.
	 * 
	 * @param integer $x
	 * @param integer $y
	 * @return sroze\Nurbs\Point
	 */
	public function getDelaunayPoint ($x, $y)
	{
		// On récupère les surfaces de Delaunay
		$triangles = $this->getDelaunaySurfaces();
		
		var_dump($triangles);
	}
	
	/**
	 * Retourne les triangles de Delaunay de la surface, à savoir des
	 * Nurbs de 3 points.
	 * 
	 * @return array of sroze\Nurbs\Nurbs
	 */
	public function getDelaunaySurfaces ()
	{
		// On vérifie qu'il y a assez de points
		if (count($this->_points) < 3) {
			throw new NurbsException(
				'Il y a moins de 3 points: impossible d\'utiliser Delaunay'
			);
		}
		
		// On utilise la librairie Delaunay pour récupère les triangles
		$triangles = Delaunay::triangulate($this->_points);
		
		return $triangles;
	}
}
