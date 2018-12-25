<?php

namespace Fbpkg;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.1
 * @package \Fbpkg
 */
abstract class UtilsFoundation
{	
	/**
	 * @var \Fbpkg\Facebook
	 */
	protected $fb;

	/**
	 * @param \Fbpkg\Facebook $fb
	 *
	 * Constructor.
	 */
	public function __construct(Facebook $fb)
	{
		$this->fb = $fb;
	}
}
