<?php
	
	/**
	 * This file is part of MVC Core framework
	 * (c) Matija Božić, www.matijabozic.com
	 * 
	 * This class enables you to paginate arbitrary content.
	 * Goal of this class is not to return formated HTML tags, but to provide
	 * you with informations about pages, so you can build your pagination in 
	 * template engine of your choice.
	 * 
	 * @package    Pagination
	 * @author     Matija Božić <matijabozic@gmx.com>
	 * @license    MIT - http://opensource.org/licenses/MIT
	 */
	 
	namespace Core\Pagination;
	
	class Pagination
	{
		/**
		 * Current page
		 * 
		 * @access  protected
		 * @var     integer
		 */
		 
		protected $page;
				
		/**
		  Total number of items to paginate
		 * 
		 * @access  protected
		 * @var     integer
		 */
		 
		protected $items;
		
		/**
		 * How many items is shown per page
		 * 
		 * @access  protected
		 * @var     integer
		 */
		 
		protected $limit;
		
		/**
		 * How many page links shoud be shown left and right from current page 
		 *
		 * @access  protected
		 * @var     integer
		 */
		 
		protected $links;
		
		/**
		 * Paginator constructor
		 * 
		 * @access  public
		 * @param   integer
		 * @param   integer
		 * @param   integer
		 * @param   integer
		 */
		
		public function __construct($page = null, $items = null, $limit = null, $links = 10)
		{
			$this->page = $page;
			$this->items = $items;
			$this->limit = $limit;
			$this->links = $links;
		}
		
		/**
		 * Set current page
		 * 
		 * @access  public
		 * @param   integer
		 * @return  void
		 */
		
		public function setPage($page)
		{
			$this->page = $page;
		}		
		
		/**
		 * Get current page
		 * 
		 * @access  public
		 * @return  integer
		 */
		
		public function getPage()
		{
			return $this->page();
		}
		
		/**
		 * Set total number of items to paginate
		 * 
		 * @access  public
		 * @param   integer
		 * @return  void
		 */
		
		public function setItems($items)
		{
			$this->items = $items;	
		}
		
		/**
		 * Get total number of items to paginate
		 * 
		 * @access  public
		 * @return  integer
		 */
		
		public function getItems()
		{
			return $this->items;
		}
		
		/**
		 * Set limit, how many items is shown per page
		 * 
		 * @access  public
		 * @param   integer
		 * @return  void
		 */
		
		public function setLimit($limit) 
		{
			$this->limit = $limit;
		}
		
		/**
		 * Get limit, how many items is shown per page
		 * 
		 * @access  public
		 * @return  void
		 */
		
		public function getLimit()
		{
			return $this->limit;
		}
		
		/**
		 * Set number of links that will be shown left and right from current page
		 * 
		 * @access  public
		 * @param   integer
		 * @return  void
		 */
		
		public function setLinks($links)
		{
			$this->links = $links;
		}
		
		/**
		 * Get number of links that will be shown left and right from current page
		 * 
		 * @access  public
		 * @return  integer 
		 */
		
		public function getLinks()
		{
			return $this->links;
		}
		
		/**
		 * Builds and returns informations about pagination
		 * 
		 * @access  public
		 * @return  array
		 */
		
		public function getPaginationInfo()
		{
			// Calculate total number of pages
			
			$pages = ceil($this->items / $this->limit);
			
			// Calculate next and back page
						
			$pageNext = $this->page + 1;
			$pageBack = $this->page - 1;
			
			// Prepare pages next and back arrays
			
			$pagesNext = array();
			$pagesBack = array();
			
			// Build pages next array
			
			for($x = 1; $x <= $this->links; $x++) {
				$n = $this->page + $x;
				if($n > $pages) {
					break;
				}
				array_push($pagesNext, $n);
			}
			
			// Optional, but might be useful in future
			array_unshift($pagesNext, null);
			unset($pagesNext[0]);
			
			// Build pages back array
			
			for($x = 1; $x <= $this->links; $x++) {
				$n = $this->page - $x;
				if($n < 1) {
					break;	
				}
				array_unshift($pagesBack, $n);
			}
			
			// Optional, but might be useful in future
			array_unshift($pagesBack, null);
			unset($pagesBack[0]);
						
			
			// Build info array that should be used in View layer
							
			$info = array();
			
			$info['pageCurrent'] = $this->page;			
			
			if($pageNext <= $pages) {
				$info['pageNext'] = $pageNext;
			}
			
			if($pageBack >= 1) {
				$info['pageBack'] = $pageBack;
			}
			
			$info['pageFirst']  = 1;
			$info['pageLast']   = $pages;
			$info['pagesNext']  = $pagesNext;
			$info['pagesBack']  = $pagesBack;
			$info['pagesTotal'] = $pages;			
			$info['pagesLimit'] = $this->limit;
			$info['itemsTotal'] = $this->items;			
			
			return $info;
		}
	}

?>