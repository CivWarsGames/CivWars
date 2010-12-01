<?php
/**
 *
 * Parent class that have some common functions with the compiler and the interpreter
 * part of the code extracted from phpbb
 */
class CodeParser
{
	protected $_content;
	protected $_blockElseLevel = array();
	protected $compiledCode = array();
	protected $filename = array();
	protected $tplRoot = '';

	protected function getFileContent($file)
	{
		//TODO handle CustomException
		$file = strtolower($file);
		$srcRoot = $this->tplRoot."src/";
		$content = file_get_contents($srcRoot.$file);
		return $content;
	}

	public function parseContent($code, $noEcho = false, $echoVar = '')
	{

		preg_match_all('#<!-- INCLUDE ([A-Z0-9\-_]+|[a-zA-Z0-9\_\-\+\./]+[\.html]+) -->#', $code, $matches);
		$includeBlocks = $matches[1];
		$code = preg_replace('#<!-- INCLUDE (\{\$?[A-Z0-9\-_]+\}|[a-zA-Z0-9\_\-\+\./]+[\.html]+) -->#', '<!-- INCLUDE -->', $code);
		//preg_match_all('#<!-- IF ([^<].*?)?-->(.*)<!-- ENDIF -->#s', $content, $matches,PREG_SET_ORDER);
		preg_match_all('#<!-- ([^<].*?) (.*?)? ?-->#', $code, $blocks, PREG_SET_ORDER);

		$textBlocks = preg_split('#<!-- [^<].*? (?:.*?)? ?-->#', $code);

		for ($i = 0, $j = sizeof($textBlocks); $i < $j; $i++)
		{
			$this->compileVarTags($textBlocks[$i]);
		}

		$compileBlocks = array();

		for ($currTb = 0, $tbSize = sizeof($blocks); $currTb < $tbSize; $currTb++)
		{
			$blockVal = &$blocks[$currTb];

			switch ($blockVal[1])
			{
				case 'BEGIN':
					$this->_blockElseLevel[] = false;
					$compileBlocks[] = '<?php ' . $this->compileTagBlock($blockVal[2]) . ' ?>';
					break;

				case 'BEGINELSE':
					$this->_blockElseLevel[sizeof($this->_blockElseLevel) - 1] = true;
					$compileBlocks[] = '<?php }} else { ?>';
					break;

				case 'END':
					array_pop($this->block_names);
					$compileBlocks[] = '<?php ' . ((array_pop($this->_blockElseLevel)) ? '}' : '}}') . ' ?>';
					break;

				case 'IF':
					$compileBlocks[] = '<?php ' . $this->compileTagIf($blockVal[2], false) . ' ?>';
					break;

				case 'ELSE':
					$compileBlocks[] = '<?php } else { ?>';
					break;

				case 'ELSEIF':
					$compileBlocks[] = '<?php ' . $this->compileTagIf($blockVal[2], true) . ' ?>';
					break;

				case 'ENDIF':
					$compileBlocks[] = '<?php } ?>';
					break;

				case 'DEFINE':
					$compileBlocks[] = '<?php ' . $this->compileTagDefine($blockVal[2], true) . ' ?>';
					break;

				case 'UNDEFINE':
					$compileBlocks[] = '<?php ' . $this->compileTagDefine($blockVal[2], false) . ' ?>';
					break;

				case 'INCLUDE':
					$temp = array_shift($includeBlocks);
					$file = $temp;
					$compileBlocks[] = '<?php ' . $this->compileTagInclude($temp) . ' ?>';

					// No point in checking variable includes
					if ($file && mb_ereg("^[A-Z0-9\-_]+$", $file))
					{
						$this->tplInclude($file, false);
					}
					break;

					/* case 'INCLUDEPHP':
					 * $compile_blocks[] = ($config['tpl_allow_php']) ? '<?php ' . $this->compile_tag_include_php(array_shift($includephp_blocks)) . ' ?>' : '';
					 * break;
					 */

					/*
					 * case 'PHP':
					 * $compile_blocks[] = ($config['tpl_allow_php']) ? '<?php ' . array_shift($php_blocks) . ' ?>' : '';
					 * break;
					 */

					

				default:
					$this->compileVarTags($blockVal[0]);
					$trimCheck = trim($blockVal[0]);
					$compileBlocks[] = (!$noEcho) ? ((!empty($trimCheck)) ? $blockVal[0] : '') : ((!empty($trimCheck)) ? $blockVal[0] : '');
					break;
			}
		}
		$templatePhp = '';
		for ($i = 0, $size = sizeof($textBlocks); $i < $size; $i++)
		{
			$trimCheckText = trim($textBlocks[$i]);
			$templatePhp .= (!$noEcho) ? (($trimCheckText != '') ? $textBlocks[$i] : '') .
			((isset($compileBlocks[$i])) ? $compileBlocks[$i] : '') : (($trimCheckText != '')
			? $textBlocks[$i] : '') . ((isset($compileBlocks[$i])) ? $compileBlocks[$i] : '');
		}
		// Remove unused opening/closing tags
		$templatePhp = str_replace(' ?><?php ', ' ', $templatePhp);
		// Now add a newline after each php closing tag which already has a newline
		// PHP itself strips a newline if a closing tag is used (this is documented behaviour) and it is mostly not intended by style authors to remove newlines
		$templatePhp = preg_replace('#\?\>([\r\n])#', '?>\1\1', $templatePhp);

		// There will be a number of occasions where we switch into and out of
		// PHP mode instantaneously. Rather than "burden" the parser with this
		// we'll strip out such occurences, minimising such switching
		if ($noEcho)
		{
			return "\$$echoVar .= '" . $templatePhp . "'";
		}
		//echo $templatePhp;
		return $templatePhp;
	}

	/**
	 *
	 * Translates the "HTML" vars into PHP vars
	 * @param String $textBlocks
	 * @param Bool $secondTime this param is to do it recursively ex: $var['STH'][$otherVar]
	 */
	private function compileVarTags(&$textBlocks,$secondTime = false)
	{

		if($secondTime == false){
			$trad = array("$" => "", "[" => "['", "]" => "']", "$(" => "$(", "$." => "$.");
			$textBlocks = strtr($textBlocks, $trad);
		}
		$textBlocks = preg_replace('#\{([A-Za-z0-9\-_\[\]\:\'\.\$]+)\}#', "'.VarsContainer::\$display\\1.'", $textBlocks);
		preg_match("#\{([A-Za-z0-9\-_\[\]\:\'\.\$]+)\}#", $textBlocks, $matches);
		if(isset($matches[0])){
			$this->compileVarTags($textBlocks,true);
		}else{
			$trad = array("''." => "", ".''" => "", "'." => "<?php echo ", ".'" => ";?>");
			$textBlocks = strtr($textBlocks, $trad);
		}

		return;
	}

	private function compileTagBlock($tagArgs)
	{
		$noNesting = false;

		// Is the designer wanting to call another loop in a loop?
		if (strpos($tagArgs, '!') === 0)
		{
			// Count the number if ! occurrences (not allowed in vars)
			$noNesting = substr_count($tagArgs, '!');
			$tagArgs = substr($tagArgs, $noNesting);
		}

		// Allow for control of looping (indexes start from zero):
		// foo(2)    : Will start the loop on the 3rd entry
		// foo(-2)   : Will start the loop two entries from the end
		// foo(3,4)  : Will start the loop on the fourth entry and end it on the fifth
		// foo(3,-4) : Will start the loop on the fourth entry and end it four from last
		if (preg_match('#^([^()]*)\(([\-\d]+)(?:,([\-\d]+))?\)$#', $tagArgs, $match))
		{
			$tagArgs = $match[1];

			if ($match[2] < 0)
			{
				$loopStart = '($_' . $tagArgs . '_count ' . $match[2] . ' < 0 ? 0 : $_' . $tagArgs . '_count ' .
				$match[2] . ')';
			}
			else
			{
				$loopStart = '($_' . $tagArgs . '_count < ' . $match[2] . ' ? $_' . $tagArgs . '_count : ' .
				$match[2] . ')';
			}

			if (strlen($match[3]) < 1 || $match[3] == -1)
			{
				$loopEnd = '$_' . $tagArgs . '_count';
			}
			else if ($match[3] >= 0)
			{
				$loopEnd = '(' . ($match[3] + 1) . ' > $_' . $tagArgs . '_count ? $_' . $tagArgs . '_count : ' .
				($match[3] + 1) . ')';
			}
			else //if ($match[3] < -1)
			{
				$loopEnd = '$_' . $tagArgs . '_count' . ($match[3] + 1);
			}
		}
		else
		{
			$loopStart = 0;
			$loopEnd = '$_' . $tagArgs . '_count';
		}

		$tagTemplatePhp = '';
		array_push($this->block_names, $tagArgs);

		if ($noNesting !== false)
		{
			// We need to implode $no_nesting times from the end...
			$block = array_slice($this->block_names, -$noNesting);
		}
		else
		{
			$block = $this->block_names;
		}

		if (sizeof($block) < 2)
		{
			// Block is not nested.
			$tagTemplate_php = '$_' . $tagArgs . "_count = (isset(\$this->_tpldata['$tagArgs'])) ?
			 sizeof(\$this->_tpldata['$tagArgs']) : 0;";
			$varref = "\$this->_tpldata['$tagArgs']";
		}
		else
		{
			// This block is nested.
			// Generate a namespace string for this block.
			$namespace = implode('.', $block);

			// Get a reference to the data array for this block that depends on the
			// current indices of all parent blocks.
			$varref = $this->generateBlockDataRef($namespace, false);

			// Create the for loop code to iterate over this block.
			$tagTemplatePhp = '$_' . $tagArgs . '_count = (isset(' . $varref . ')) ? sizeof(' . $varref . ') : 0;';
		}

		$tagTemplatePhp .= 'if ($_' . $tagArgs . '_count) {';

		/**
		 * The following uses foreach for iteration instead of a for loop, foreach is faster but requires PHP to make a copy of the contents of the array which uses more memory
		 * <code>
		 *	if (!$offset)
		 *	{
		 *		$tag_template_php .= 'foreach (' . $varref . ' as $_' . $tag_args . '_i => $_' . $tag_args . '_val){';
		 *	}
		 * </code>
		 */

		$tagTemplatePhp .= 'for ($_' . $tagArgs . '_i = ' . $loopStart . '; $_' . $tagArgs . '_i < ' . $loopEnd . '; ++$_' . $tag_args . '_i){';
		$tagTemplatePhp .= '$_'. $tagArgs . '_val = &' . $varref . '[$_'. $tagArgs. '_i];';

		return $tagTemplatePhp;
	}
	private function compileTagIf($tagArgs, $elseif)
	{

		// Tokenize args for 'if' tag.
		preg_match_all('/(?:
			"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"         |
			\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'     |
			[(),]                                  |
			[^\s(),]+)/x', $tagArgs, $match);

		$tokens = $match[0];
		$isArgStack = array();

		for ($i = 0, $size = sizeof($tokens); $i < $size; $i++)
		{
			$token = &$tokens[$i];

			switch ($token)
			{
				case '!==':
				case '===':
				case '<<':
				case '>>':
				case '|':
				case '^':
				case '&':
				case '~':
				case ')':
				case ',':
				case '+':
				case '-':
				case '*':
				case '/':
				case '@':
					break;

				case '==':
				case 'eq':
					$token = '==';
					break;

				case '!=':
				case '<>':
				case 'ne':
				case 'neq':
					$token = '!=';
					break;

				case '<':
				case 'lt':
					$token = '<';
					break;

				case '<=':
				case 'le':
				case 'lte':
					$token = '<=';
					break;

				case '>':
				case 'gt':
					$token = '>';
					break;

				case '>=':
				case 'ge':
				case 'gte':
					$token = '>=';
					break;

				case '&&':
				case 'and':
					$token = '&&';
					break;

				case '||':
				case 'or':
					$token = '||';
					break;

				case '!':
				case 'not':
					$token = '!';
					break;

				case '%':
				case 'mod':
					$token = '%';
					break;

				case '(':
					array_push($isArgStack, $i);
					break;

				case 'is':
					$isArgStart = ($tokens[$i-1] == ')') ? array_pop($isArgStack) : $i-1;
					$isArg	= implode('	', array_slice($tokens,	$isArgStart, $i -	$isArgStart));

					$newTokens	= $this->ParseIsExpr($isArg, array_slice($tokens, $i+1));

					array_splice($tokens, $isArgStart, sizeof($tokens), $newTokens);

					$i = $isArgStart;

					// no break

				default:
					//if (preg_match('#^((?:[a-z0-9\-_]+\.)+)?(\$)?(?=[A-Z])([A-Z0-9\-_]+)#s', $token, $varrefs))
					if(preg_match('#\{([A-Za-z0-9\-_\[\]\:\'\.\$]+)\}#',$token, $varrefs))
					{
						$secondTime = false;
						$repeat = true;

						while($repeat){
							if($secondTime == false){
								$trad = array("$" => "", "[" => "['", "]" => "']");
								$token = strtr($token, $trad);
								$secondTime = true;
							}
							$token = preg_replace('#\{([A-Za-z0-9\-_\[\]\:\'\.\$]+)\}#', "'.VarsContainer::\$display\\1.'", $token);

							preg_match("#\{([A-Za-z0-9\-_\[\]\:\'\.\$]+)\}#", $token, $matches);
							if(!isset($matches[0])){
								
								$trad = array("''." => "", ".''" => "", "'." => "", ".'" => "");
								$token = strtr($token, $trad);
								$repeat = false;
							}
							//echo $token;
						}

					}
					else if (preg_match('#^\.((?:[a-z0-9\-_]+\.?)+)$#s', $token, $varrefs))
					{
						// Allow checking if loops are set with .loopname
						// It is also possible to check the loop count by doing <!-- IF .loopname > 1 --> for example
						$blocks = explode('.', $varrefs[1]);

						// If the block is nested, we have a reference that we can grab.
						// If the block is not nested, we just go and grab the block from _tpldata
						if (sizeof($blocks) > 1)
						{
							$block = array_pop($blocks);
							$namespace = implode('.', $blocks);
							$varref = $this->generateBlockDataRef($namespace, true);

							// Add the block reference for the last child.
							$varref .= "['" . $block . "']";
						}
						else
						{
							$varref = '$this->_tpldata';

							// Add the block reference for the last child.
							$varref .= "['" . $blocks[0] . "']";
						}
						$token = "sizeof($varref)";
					}
					else if (!empty($token))
					{
						$token = '(' . $token . ')';
					}

					break;
			}
		}

		// If there are no valid tokens left or only control/compare characters left, we do skip this statement
		if (!sizeof($tokens) || str_replace(array(' ', '=', '!', '<', '>', '&', '|', '%', '(', ')'), '',
		implode('', $tokens)) == '')
		{
			$tokens = array('false');
		}
		return (($elseif) ? '} else if (' : 'if (') . (implode(' ', $tokens) . ') { ');
	}
	private function compileTagDefine($tagArgs, $op)
	{
		preg_match('#^((?:[a-z0-9\-_]+\.)+)?\$(?=[A-Z])([A-Z0-9_\-]*)(?: = (\'?)([^\']*)(\'?))?$#',
		$tagArgs, $match);

		if (empty($match[2]) || (!isset($match[4]) && $op))
		{
			return '';
		}

		if (!$op)
		{
			return 'unset(' . (($match[1]) ? $this->generateBlockDataRef(substr($match[1], 0, -1),
			true, true) . '[\'' . $match[2] . '\']' : '$this->_tpldata[\'DEFINE\'][\'.\'][\'' . $match[2] . '\']')
			. ');';
		}

		// Are we a string?
		if ($match[3] && $match[5])
		{
			$match[4] = str_replace(array('\\\'', '\\\\', '\''), array('\'', '\\', '\\\''), $match[4]);

			// Compile reference, we allow template variables in defines...
			$match[4] = $this->compile($match[4]);

			// Now replace the php code
			$match[4] = "'" . str_replace(array('<?php echo ', '; ?>'), array("' . ", " . '"), $match[4]) . "'";
		}
		else
		{
			preg_match('#true|false|\.#i', $match[4], $type);

			switch (strtolower($type[0]))
			{
				case 'true':
				case 'false':
					$match[4] = strtoupper($match[4]);
					break;

				case '.':
					$match[4] = doubleval($match[4]);
					break;

				default:
					$match[4] = intval($match[4]);
					break;
			}
		}

		return (($match[1]) ? $this->generateBlockDataRef(substr($match[1], 0, -1), true, true)
		. '[\'' . $match[2] . '\']' : '$this->_tpldata[\'DEFINE\'][\'.\'][\'' . $match[2] . '\']')
		. ' = ' . $match[4] . ';';

	}
	/**
	 * parse expression
	 * This is from Smarty
	 * @access private
	 */
	private function parseIsExpr($isArg, $tokens)
	{
		$exprEnd = 0;
		$negateExpr = false;

		if (($firstToken = array_shift($tokens)) == 'not')
		{
			$negate_expr = true;
			$expr_type = array_shift($tokens);
		}
		else
		{
			$exprType = $firstToken;
		}

		switch ($exprType)
		{
			case 'even':
				if (@$tokens[$exprEnd] == 'by')
				{
					$exprEnd++;
					$exprArg = $tokens[$exprEnd++];
					$expr = "!(($isArg / $exprArg) % $exprArg)";
				}
				else
				{
					$expr = "!($isArg & 1)";
				}
				break;

			case 'odd':
				if (@$tokens[$exprEnd] == 'by')
				{
					$exprEnd++;
					$exprArg = $tokens[$exprEnd++];
					$expr = "(($isArg / $exprArg) % $exprArg)";
				}
				else
				{
					$expr = "($isArg & 1)";
				}
				break;

			case 'div':
				if (@$tokens[$exprEnd] == 'by')
				{
					$exprEnd++;
					$exprArg = $tokens[$exprEnd++];
					$expr = "!($isArg % $exprArg)";
				}
				break;
		}

		if ($negateExpr)
		{
			$expr = "!($expr)";
		}

		array_splice($tokens, 0, $exprEnd, $expr);

		return $tokens;
	}

	/**
	 * Generates a reference to the given variable inside the given (possibly nested)
	 * block namespace. This is a string of the form:
	 * ' . $this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['varname'] . '
	 * It's ready to be inserted into an "echo" line in one of the templates.
	 * NOTE: expects a trailing "." on the namespace.
	 * @access private
	 */
	private function generateBlockVarref($namespace, $varname, $echo = true, $defop = false)
	{
		// Strip the trailing period.
		$namespace = substr($namespace, 0, -1);

		// Get a reference to the data block for this namespace.
		$varref = $this->generateBlockDataRef($namespace, true, $defop);
		// Prepend the necessary code to stick this in an echo line.

		// Append the variable reference.
		$varref .= "['$varname']";
		$varref = ($echo) ? "<?php echo $varref; ?>" : ((isset($varref)) ? $varref : '');

		return $varref;
	}

	/**
	 * Generates a reference to the array of data values for the given
	 * (possibly nested) block namespace. This is a string of the form:
	 * $this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['$childN']
	 *
	 * If $include_last_iterator is true, then [$_childN_i] will be appended to the form shown above.
	 * NOTE: does not expect a trailing "." on the blockname.
	 * @access private
	 */
	private function generateBlockDataRef($blockname, $includeLastIterator, $defop = false)
	{
		// Get an array of the blocks involved.
		$blocks = explode('.', $blockname);
		$blockcount = sizeof($blocks) - 1;

		// DEFINE is not an element of any referenced variable, we must use _tpldata to access it
		if ($defop)
		{
			$varref = '$this->_tpldata[\'DEFINE\']';
			// Build up the string with everything but the last child.
			for ($i = 0; $i < $blockcount; $i++)
			{
				$varref .= "['" . $blocks[$i] . "'][\$_" . $blocks[$i] . '_i]';
			}
			// Add the block reference for the last child.
			$varref .= "['" . $blocks[$blockcount] . "']";
			// Add the iterator for the last child if requried.
			if ($includeLastIterator)
			{
				$varref .= '[$_' . $blocks[$blockcount] . '_i]';
			}
			return $varref;
		}
		else if ($includeLastIterator)
		{
			return '$_'. $blocks[$blockcount] . '_val';
		}
		else
		{
			return '$_'. $blocks[$blockcount - 1] . '_val[\''. $blocks[$blockcount]. '\']';
		}
	}


}
?>