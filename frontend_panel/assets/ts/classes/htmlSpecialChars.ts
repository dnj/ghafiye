type QuotaStyle = 'ENT_NOQUOTES' | 'ENT_HTML_QUOTE_SINGLE' | 'ENT_HTML_QUOTE_DOUBLE' | 'ENT_COMPAT' | 'ENT_QUOTES' | 'ENT_IGNORE' | number | any;
export default function htmlSpecialChars (string:string, quoteStyle?:QuotaStyle, charset:string = 'utf-8', doubleEncode:boolean = false) {
	//       discuss at: http://locutus.io/php/htmlspecialchars/
	//      original by: Mirek Slugen
	//      improved by: Kevin van Zonneveld (http://kvz.io)
	//      bugfixed by: Nathan
	//      bugfixed by: Arno
	//      bugfixed by: Brett Zamir (http://brett-zamir.me)
	//      bugfixed by: Brett Zamir (http://brett-zamir.me)
	//       revised by: Kevin van Zonneveld (http://kvz.io)
	//         input by: Ratheous
	//         input by: Mailfaker (http://www.weedem.fr/)
	//         input by: felix
	// reimplemented by: Brett Zamir (http://brett-zamir.me)
	//           note 1: charset argument not supported
	//        example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES')
	//        returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
	//        example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES'])
	//        returns 2: 'ab"c&#039;d'
	//        example 3: htmlspecialchars('my "&entity;" is still here', null, null, false)
	//        returns 3: 'my &quot;&entity;&quot; is still here'
  
	var optTemp = 0;
	var i = 0;
	var noquotes = false;
	
	const OPTS = {
	'ENT_NOQUOTES': 0,
	'ENT_HTML_QUOTE_SINGLE': 1,
	'ENT_HTML_QUOTE_DOUBLE': 2,
	'ENT_COMPAT': 2,
	'ENT_QUOTES': 3,
	'ENT_IGNORE': 4
	}
	if (typeof quoteStyle === 'undefined' || quoteStyle === null) {
	  quoteStyle = OPTS['ENT_QUOTES'];
	}
	string = string || '';
	string = string.toString();
  
	if (doubleEncode !== false) {
	  // Put this first to avoid double-encoding
	  string = string.replace(/&/g, '&amp;');
	}
  
	string = string
	  .replace(/</g, '&lt;')
	  .replace(/>/g, '&gt;');
	if (quoteStyle === 0) {
	  noquotes = true;
	}
	if (typeof quoteStyle !== 'number') {
	  // Allow for a single string or an array of string flags
	  quoteStyle = [].concat(quoteStyle);
	  for (i = 0; i < quoteStyle.length; i++) {
		// Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
		if (OPTS[quoteStyle[i]] === 0) {
		  noquotes = true;
		} else if (OPTS[quoteStyle[i]]) {
		  optTemp = optTemp | OPTS[quoteStyle[i]];
		}
	  }
	  quoteStyle = optTemp;
	}
	if (quoteStyle & OPTS.ENT_HTML_QUOTE_SINGLE) {
	  string = string.replace(/'/g, '&#039;');
	}
	if (!noquotes) {
	  string = string.replace(/"/g, '&quot;');
	}
  
	return string;
  }