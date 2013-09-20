<?php

App::uses('PaginatorHelper', 'View/Helper');
require_once(dirname(__FILE__) . '/../../Lib/BootstrapInfo.php');

class BootstrapPaginatorHelper extends PaginatorHelper {

	public $helpers = array('Html');

	public function __construct(View $View, $options = array()) {
		parent::__construct($View, $options);
		$this->BootstrapInfo = new BootstrapInfo();
	}

	public function next($text = '&gt;', $opt = array(), $disabledText = '&gt;', $disabledOpt = array()) {
		$opt['tag'] = $disabledOpt['tag'] = 'li';
		$opt['escape'] = $disabledOpt['escape'] = false;
		$next = parent::next($text, $opt, $disabledText, $disabledOpt);
		$next = str_replace(array('<span class="next">', '</span>'), '', $next);
		if (!parent::hasNext()) {
			$next = '<li href="#" class="disabled" rel="next"><a>' . trim($text) . '</a></li>';
		}
		return $next;
	}

	public function prev($text = '&lt;', $opt = array(), $disabledText = '&lt;', $disabledOpt = array()) {
		$opt['tag'] = $disabledOpt['tag'] = 'li';
		$opt['escape'] = $disabledOpt['escape'] = false;
		$prev = parent::prev($text, $opt, $disabledText, $disabledOpt);
		$prev = str_replace(array('<span class="prev">', '</span>'), '', $prev);
		if (!parent::hasPrev()) {
			$prev = '<li href="#" class="disabled" rel="prev"><a>' . trim($text) . '</a></li>';
		}
		return $prev;
	}

	public function first($text = '&lt;&lt;', $options = array()) {
		$options['escape'] = false;
		return str_replace(array('<span>', '</span>'), '', parent::first($text, $options));
	}

	public function last($text = '&gt;&gt;', $options = array()) {
		$options['escape'] = false;
		return str_replace(array('<span>', '</span>'), '', parent::last($text, $options));
	}

	public function numbers($options = array()) {
		$options['separator'] = '';
		$options['currentClass'] = 'active';
		$numbers = parent::numbers($options);
		$params = parent::params();
		if (isset($params['maxPages'])) {
			$needle = '>'.$params['maxPages'].'</a></span>';
			$needle2 = '>'.$params['maxPages'].'</span>';
			if (strpos($numbers, $needle) !== false) {
				$numbers = strstr($numbers, $needle, true);
				$numbers = $numbers . $needle;
			}
			if (strpos($numbers, $needle2) !== false) {
				$numbers = strstr($numbers, $needle2, true);
				$numbers = $numbers . $needle2;
			}
		}
		return str_replace(
			array('<span>', '<span class="active">', '</span>'),
			array('<li>', '<li class="active"><span>', '</span></li>'),
			$numbers
		);
	}	

	public function pagination($options = array()) {
		if (isset($options['maxPages']) && is_int($options['maxPages'])) {
			$model = parent::defaultModel();
			$this->request->params['paging'][$model]['maxPages'] = $options['maxPages'];
			if (isset($this->request->params['paging']) && !empty($this->request->params['paging'][$model])) {
				$params = $this->request->params['paging'][$model];
				$this->request->params['paging'][$model]['count'] = $options['maxPages'] * $params['limit'];
				if ($params['page'] > $options['maxPages']) {
					$this->request->params['paging'][$model]['page'] = $options['maxPages'];
					$this->request->params['paging'][$model]['options']['page'] = $options['maxPages'];
					$this->request->params['paging'][$model]['nextPage'] = false;
				}
			}
		}
		if (!parent::hasPrev() && !parent::hasNext()) {
			return null;
		}
		$klass = isset($options['class']) ? $options['class'] : '';
		$align = $full = null;
		if (isset($options['align'])) {
			$align = $options['align'];
			unset($options['align']);
		}
		if (isset($options['full'])) {
			$full = $options['full'];
			unset($options['full']);
		}
		$options['class'] = $this->BootstrapInfo->stylesFor('pagination', $align, $klass);
		$pages = '';
		if ($full) { $pages .= '<li>' . $this->first() . '</li>'; }
		$pages .= '<li>' . $this->prev() . '</li>';
		$numbers = $this->numbers();
		$pages .= str_replace(array('<span>', '</span>'), array('<li>', '</li>'), $numbers);		
		$pages = str_replace('<li><a href="#" class="active">', '<li class="active"><a href="#">', $pages);
		$pages .= '<li>' . $this->next() . '</li>';
		if ($full) { $pages .= '<li>' . $this->last() . '</li>'; }
		return $this->Html->tag('div', "<ul>{$pages}</ul>", $options);
	}

	public function pager($options = array()) {
		$klass = isset($options['class']) ? $options['class'] : '';
		$options['class'] = $this->BootstrapInfo->stylesFor('pager', '', $klass);
		$align = false;
		$newer = 'Newer &rarr;';
		$older = '&larr; Older';
		if (isset($options['newer'])) {
			$newer = $options['newer'];
			unset($options['newer']);
		}
		if (isset($options['older'])) {
			$older = $options['older'];
			unset($options['older']);
		}
		if (isset($options['align'])) {
			$align = $options['align'];
			unset($options['align']);
		}
		$prev = '<li';
		if ($align) { $prev .= ' class="previous"'; }
		$prev .= '>' . $this->prev($older, array(), $older, array()) . '</li>';
		$next = '<li';
		if ($align) { $next .= ' class="next"'; }
		$next .= '>' . $this->next($newer, array(), $newer, array()) . '</li>';
		return $this->Html->tag('ul', $prev . $next, $options);
	}

}

