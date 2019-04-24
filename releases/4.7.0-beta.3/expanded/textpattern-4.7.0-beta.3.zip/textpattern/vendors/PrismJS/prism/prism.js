var _self="undefined"!=typeof window?window:"undefined"!=typeof WorkerGlobalScope&&self instanceof WorkerGlobalScope?self:{},Prism=function(){var o=/\blang(?:uage)?-(\w+)\b/i,n=0,R=_self.Prism={manual:_self.Prism&&_self.Prism.manual,disableWorkerMessageHandler:_self.Prism&&_self.Prism.disableWorkerMessageHandler,util:{encode:function(e){return e instanceof s?new s(e.type,R.util.encode(e.content),e.alias):"Array"===R.util.type(e)?e.map(R.util.encode):e.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/\u00a0/g," ")},type:function(e){return Object.prototype.toString.call(e).match(/\[object (\w+)\]/)[1]},objId:function(e){return e.__id||Object.defineProperty(e,"__id",{value:++n}),e.__id},clone:function(e,t){var n=R.util.type(e);switch(t=t||{},n){case"Object":if(t[R.util.objId(e)])return t[R.util.objId(e)];var a={};for(var i in t[R.util.objId(e)]=a,e)e.hasOwnProperty(i)&&(a[i]=R.util.clone(e[i],t));return a;case"Array":if(t[R.util.objId(e)])return t[R.util.objId(e)];a=[];return t[R.util.objId(e)]=a,e.forEach(function(e,n){a[n]=R.util.clone(e,t)}),a}return e}},languages:{extend:function(e,n){var t=R.util.clone(R.languages[e]);for(var a in n)t[a]=n[a];return t},insertBefore:function(t,e,n,a){var i=(a=a||R.languages)[t];if(2==arguments.length){for(var r in n=e)n.hasOwnProperty(r)&&(i[r]=n[r]);return i}var s={};for(var l in i)if(i.hasOwnProperty(l)){if(l==e)for(var r in n)n.hasOwnProperty(r)&&(s[r]=n[r]);s[l]=i[l]}return R.languages.DFS(R.languages,function(e,n){n===a[t]&&e!=t&&(this[e]=s)}),a[t]=s},DFS:function(e,n,t,a){for(var i in a=a||{},e)e.hasOwnProperty(i)&&(n.call(e,i,e[i],t||i),"Object"!==R.util.type(e[i])||a[R.util.objId(e[i])]?"Array"!==R.util.type(e[i])||a[R.util.objId(e[i])]||(a[R.util.objId(e[i])]=!0,R.languages.DFS(e[i],n,i,a)):(a[R.util.objId(e[i])]=!0,R.languages.DFS(e[i],n,null,a)))}},plugins:{},highlightAll:function(e,n){R.highlightAllUnder(document,e,n)},highlightAllUnder:function(e,n,t){var a={callback:t,selector:'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'};R.hooks.run("before-highlightall",a);for(var i,r=a.elements||e.querySelectorAll(a.selector),s=0;i=r[s++];)R.highlightElement(i,!0===n,a.callback)},highlightElement:function(e,n,t){for(var a,i,r=e;r&&!o.test(r.className);)r=r.parentNode;r&&(a=(r.className.match(o)||[,""])[1].toLowerCase(),i=R.languages[a]),e.className=e.className.replace(o,"").replace(/\s+/g," ")+" language-"+a,e.parentNode&&(r=e.parentNode,/pre/i.test(r.nodeName)&&(r.className=r.className.replace(o,"").replace(/\s+/g," ")+" language-"+a));var s={element:e,language:a,grammar:i,code:e.textContent};if(R.hooks.run("before-sanity-check",s),!s.code||!s.grammar)return s.code&&(R.hooks.run("before-highlight",s),s.element.textContent=s.code,R.hooks.run("after-highlight",s)),void R.hooks.run("complete",s);if(R.hooks.run("before-highlight",s),n&&_self.Worker){var l=new Worker(R.filename);l.onmessage=function(e){s.highlightedCode=e.data,R.hooks.run("before-insert",s),s.element.innerHTML=s.highlightedCode,t&&t.call(s.element),R.hooks.run("after-highlight",s),R.hooks.run("complete",s)},l.postMessage(JSON.stringify({language:s.language,code:s.code,immediateClose:!0}))}else s.highlightedCode=R.highlight(s.code,s.grammar,s.language),R.hooks.run("before-insert",s),s.element.innerHTML=s.highlightedCode,t&&t.call(e),R.hooks.run("after-highlight",s),R.hooks.run("complete",s)},highlight:function(e,n,t){var a={text:e,grammar:n,language:t};return a.tokens=R.tokenize(e,n),R.hooks.run("after-tokenize",a),s.stringify(R.util.encode(a.tokens),t)},matchGrammar:function(e,n,t,a,i,r,s){var l=R.Token;for(var o in t)if(t.hasOwnProperty(o)&&t[o]){if(o==s)return;var g=t[o];g="Array"===R.util.type(g)?g:[g];for(var u=0;u<g.length;++u){var d=g[u],p=d.inside,c=!!d.lookbehind,h=!!d.greedy,m=0,f=d.alias;if(h&&!d.pattern.global){var b=d.pattern.toString().match(/[imuy]*$/)[0];d.pattern=RegExp(d.pattern.source,b+"g")}d=d.pattern||d;for(var k=a,y=i;k<n.length;y+=n[k].length,++k){var x=n[k];if(n.length>e.length)return;if(!(x instanceof l)){d.lastIndex=0;var w=1;if(!(F=d.exec(x))&&h&&k!=n.length-1){if(d.lastIndex=y,!(F=d.exec(e)))break;for(var v=F.index+(c?F[1].length:0),P=F.index+F[0].length,_=k,S=y,$=n.length;_<$&&(S<P||!n[_].type&&!n[_-1].greedy);++_)(S+=n[_].length)<=v&&(++k,y=S);if(n[k]instanceof l||n[_-1].greedy)continue;w=_-k,x=e.slice(y,S),F.index-=y}if(F){c&&(m=F[1]?F[1].length:0);P=(v=F.index+m)+(F=F[0].slice(m)).length;var F,E=x.slice(0,v),A=x.slice(P),j=[k,w];E&&(++k,y+=E.length,j.push(E));var C=new l(o,p?R.tokenize(F,p):F,f,F,h);if(j.push(C),A&&j.push(A),Array.prototype.splice.apply(n,j),1!=w&&R.matchGrammar(e,n,t,k,y,!0,o),r)break}else if(r)break}}}}},tokenize:function(e,n,t){var a=[e],i=n.rest;if(i){for(var r in i)n[r]=i[r];delete n.rest}return R.matchGrammar(e,a,n,0,0,!1),a},hooks:{all:{},add:function(e,n){var t=R.hooks.all;t[e]=t[e]||[],t[e].push(n)},run:function(e,n){var t=R.hooks.all[e];if(t&&t.length)for(var a,i=0;a=t[i++];)a(n)}}},s=R.Token=function(e,n,t,a,i){this.type=e,this.content=n,this.alias=t,this.length=0|(a||"").length,this.greedy=!!i};if(s.stringify=function(n,t,e){if("string"==typeof n)return n;if("Array"===R.util.type(n))return n.map(function(e){return s.stringify(e,t,n)}).join("");var a={type:n.type,content:s.stringify(n.content,t,e),tag:"span",classes:["token",n.type],attributes:{},language:t,parent:e};if(n.alias){var i="Array"===R.util.type(n.alias)?n.alias:[n.alias];Array.prototype.push.apply(a.classes,i)}R.hooks.run("wrap",a);var r=Object.keys(a.attributes).map(function(e){return e+'="'+(a.attributes[e]||"").replace(/"/g,"&quot;")+'"'}).join(" ");return"<"+a.tag+' class="'+a.classes.join(" ")+'"'+(r?" "+r:"")+">"+a.content+"</"+a.tag+">"},!_self.document)return _self.addEventListener&&(R.disableWorkerMessageHandler||_self.addEventListener("message",function(e){var n=JSON.parse(e.data),t=n.language,a=n.code,i=n.immediateClose;_self.postMessage(R.highlight(a,R.languages[t],t)),i&&_self.close()},!1)),_self.Prism;var e=document.currentScript||[].slice.call(document.getElementsByTagName("script")).pop();return e&&(R.filename=e.src,R.manual||e.hasAttribute("data-manual")||("loading"!==document.readyState?window.requestAnimationFrame?window.requestAnimationFrame(R.highlightAll):window.setTimeout(R.highlightAll,16):document.addEventListener("DOMContentLoaded",R.highlightAll))),_self.Prism}();"undefined"!=typeof module&&module.exports&&(module.exports=Prism),"undefined"!=typeof global&&(global.Prism=Prism),Prism.languages.markup={comment:/<!--[\s\S]*?-->/,prolog:/<\?[\s\S]+?\?>/,doctype:/<!DOCTYPE[\s\S]+?>/i,cdata:/<!\[CDATA\[[\s\S]*?]]>/i,tag:{pattern:/<\/?(?!\d)[^\s>\/=$<%]+(?:\s+[^\s>\/=]+(?:=(?:("|')(?:\\[\s\S]|(?!\1)[^\\])*\1|[^\s'">=]+))?)*\s*\/?>/i,greedy:!0,inside:{tag:{pattern:/^<\/?[^\s>\/]+/i,inside:{punctuation:/^<\/?/,namespace:/^[^\s>\/:]+:/}},"attr-value":{pattern:/=(?:("|')(?:\\[\s\S]|(?!\1)[^\\])*\1|[^\s'">=]+)/i,inside:{punctuation:[/^=/,{pattern:/(^|[^\\])["']/,lookbehind:!0}]}},punctuation:/\/?>/,"attr-name":{pattern:/[^\s>\/]+/,inside:{namespace:/^[^\s>\/:]+:/}}}},entity:/&#?[\da-z]{1,8};/i},Prism.languages.markup.tag.inside["attr-value"].inside.entity=Prism.languages.markup.entity,Prism.hooks.add("wrap",function(e){"entity"===e.type&&(e.attributes.title=e.content.replace(/&amp;/,"&"))}),Prism.languages.xml=Prism.languages.markup,Prism.languages.html=Prism.languages.markup,Prism.languages.mathml=Prism.languages.markup,Prism.languages.svg=Prism.languages.markup,Prism.languages.css={comment:/\/\*[\s\S]*?\*\//,atrule:{pattern:/@[\w-]+?.*?(?:;|(?=\s*\{))/i,inside:{rule:/@[\w-]+/}},url:/url\((?:(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1|.*?)\)/i,selector:/[^{}\s][^{};]*?(?=\s*\{)/,string:{pattern:/("|')(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/,greedy:!0},property:/[-_a-z\xA0-\uFFFF][-\w\xA0-\uFFFF]*(?=\s*:)/i,important:/\B!important\b/i,function:/[-a-z0-9]+(?=\()/i,punctuation:/[(){};:]/},Prism.languages.css.atrule.inside.rest=Prism.languages.css,Prism.languages.markup&&(Prism.languages.insertBefore("markup","tag",{style:{pattern:/(<style[\s\S]*?>)[\s\S]*?(?=<\/style>)/i,lookbehind:!0,inside:Prism.languages.css,alias:"language-css",greedy:!0}}),Prism.languages.insertBefore("inside","attr-value",{"style-attr":{pattern:/\s*style=("|')(?:\\[\s\S]|(?!\1)[^\\])*\1/i,inside:{"attr-name":{pattern:/^\s*style/i,inside:Prism.languages.markup.tag.inside},punctuation:/^\s*=\s*['"]|['"]\s*$/,"attr-value":{pattern:/.+/i,inside:Prism.languages.css}},alias:"language-css"}},Prism.languages.markup.tag)),Prism.languages.clike={comment:[{pattern:/(^|[^\\])\/\*[\s\S]*?(?:\*\/|$)/,lookbehind:!0},{pattern:/(^|[^\\:])\/\/.*/,lookbehind:!0}],string:{pattern:/(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/,greedy:!0},"class-name":{pattern:/((?:\b(?:class|interface|extends|implements|trait|instanceof|new)\s+)|(?:catch\s+\())[\w.\\]+/i,lookbehind:!0,inside:{punctuation:/[.\\]/}},keyword:/\b(?:if|else|while|do|for|return|in|instanceof|function|new|try|throw|catch|finally|null|break|continue)\b/,boolean:/\b(?:true|false)\b/,function:/[a-z0-9_]+(?=\()/i,number:/\b0x[\da-f]+\b|(?:\b\d+\.?\d*|\B\.\d+)(?:e[+-]?\d+)?/i,operator:/--?|\+\+?|!=?=?|<=?|>=?|==?=?|&&?|\|\|?|\?|\*|\/|~|\^|%/,punctuation:/[{}[\];(),.:]/},Prism.languages.javascript=Prism.languages.extend("clike",{keyword:/\b(?:as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|var|void|while|with|yield)\b/,number:/\b(?:0[xX][\dA-Fa-f]+|0[bB][01]+|0[oO][0-7]+|NaN|Infinity)\b|(?:\b\d+\.?\d*|\B\.\d+)(?:[Ee][+-]?\d+)?/,function:/[_$a-z\xA0-\uFFFF][$\w\xA0-\uFFFF]*(?=\s*\()/i,operator:/-[-=]?|\+[+=]?|!=?=?|<<?=?|>>?>?=?|=(?:==?|>)?|&[&=]?|\|[|=]?|\*\*?=?|\/=?|~|\^=?|%=?|\?|\.{3}/}),Prism.languages.insertBefore("javascript","keyword",{regex:{pattern:/(^|[^/])\/(?!\/)(\[[^\]\r\n]+]|\\.|[^/\\\[\r\n])+\/[gimyu]{0,5}(?=\s*($|[\r\n,.;})]))/,lookbehind:!0,greedy:!0},"function-variable":{pattern:/[_$a-z\xA0-\uFFFF][$\w\xA0-\uFFFF]*(?=\s*=\s*(?:function\b|(?:\([^()]*\)|[_$a-z\xA0-\uFFFF][$\w\xA0-\uFFFF]*)\s*=>))/i,alias:"function"}}),Prism.languages.insertBefore("javascript","string",{"template-string":{pattern:/`(?:\\[\s\S]|[^\\`])*`/,greedy:!0,inside:{interpolation:{pattern:/\$\{[^}]+\}/,inside:{"interpolation-punctuation":{pattern:/^\$\{|\}$/,alias:"punctuation"},rest:Prism.languages.javascript}},string:/[\s\S]+/}}}),Prism.languages.markup&&Prism.languages.insertBefore("markup","tag",{script:{pattern:/(<script[\s\S]*?>)[\s\S]*?(?=<\/script>)/i,lookbehind:!0,inside:Prism.languages.javascript,alias:"language-javascript",greedy:!0}}),Prism.languages.js=Prism.languages.javascript,"undefined"!=typeof self&&self.Prism&&self.document&&document.querySelector&&(self.Prism.fileHighlight=function(){var o={js:"javascript",py:"python",rb:"ruby",ps1:"powershell",psm1:"powershell",sh:"bash",bat:"batch",h:"c",tex:"latex"};Array.prototype.slice.call(document.querySelectorAll("pre[data-src]")).forEach(function(e){for(var n,t=e.getAttribute("data-src"),a=e,i=/\blang(?:uage)?-(?!\*)(\w+)\b/i;a&&!i.test(a.className);)a=a.parentNode;if(a&&(n=(e.className.match(i)||[,""])[1]),!n){var r=(t.match(/\.(\w+)$/)||[,""])[1];n=o[r]||r}var s=document.createElement("code");s.className="language-"+n,e.textContent="",s.textContent="Loading…",e.appendChild(s);var l=new XMLHttpRequest;l.open("GET",t,!0),l.onreadystatechange=function(){4==l.readyState&&(l.status<400&&l.responseText?(s.textContent=l.responseText,Prism.highlightElement(s)):400<=l.status?s.textContent="✖ Error "+l.status+" while fetching file: "+l.statusText:s.textContent="✖ Error: File does not exist or is empty")},l.send(null)})},document.addEventListener("DOMContentLoaded",self.Prism.fileHighlight)),Prism.languages.json={property:/"(?:\\.|[^\\"\r\n])*"(?=\s*:)/i,string:{pattern:/"(?:\\.|[^\\"\r\n])*"(?!\s*:)/,greedy:!0},number:/\b0x[\dA-Fa-f]+\b|(?:\b\d+\.?\d*|\B\.\d+)(?:[Ee][+-]?\d+)?/,punctuation:/[{}[\]);,]/,operator:/:/g,boolean:/\b(?:true|false)\b/i,null:/\bnull\b/i},Prism.languages.jsonp=Prism.languages.json,Prism.languages.markdown=Prism.languages.extend("markup",{}),Prism.languages.insertBefore("markdown","prolog",{blockquote:{pattern:/^>(?:[\t ]*>)*/m,alias:"punctuation"},code:[{pattern:/^(?: {4}|\t).+/m,alias:"keyword"},{pattern:/``.+?``|`[^`\n]+`/,alias:"keyword"}],title:[{pattern:/\w+.*(?:\r?\n|\r)(?:==+|--+)/,alias:"important",inside:{punctuation:/==+$|--+$/}},{pattern:/(^\s*)#+.+/m,lookbehind:!0,alias:"important",inside:{punctuation:/^#+|#+$/}}],hr:{pattern:/(^\s*)([*-])(?:[\t ]*\2){2,}(?=\s*$)/m,lookbehind:!0,alias:"punctuation"},list:{pattern:/(^\s*)(?:[*+-]|\d+\.)(?=[\t ].)/m,lookbehind:!0,alias:"punctuation"},"url-reference":{pattern:/!?\[[^\]]+\]:[\t ]+(?:\S+|<(?:\\.|[^>\\])+>)(?:[\t ]+(?:"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\((?:\\.|[^)\\])*\)))?/,inside:{variable:{pattern:/^(!?\[)[^\]]+/,lookbehind:!0},string:/(?:"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\((?:\\.|[^)\\])*\))$/,punctuation:/^[\[\]!:]|[<>]/},alias:"url"},bold:{pattern:/(^|[^\\])(\*\*|__)(?:(?:\r?\n|\r)(?!\r?\n|\r)|.)+?\2/,lookbehind:!0,inside:{punctuation:/^\*\*|^__|\*\*$|__$/}},italic:{pattern:/(^|[^\\])([*_])(?:(?:\r?\n|\r)(?!\r?\n|\r)|.)+?\2/,lookbehind:!0,inside:{punctuation:/^[*_]|[*_]$/}},url:{pattern:/!?\[[^\]]+\](?:\([^\s)]+(?:[\t ]+"(?:\\.|[^"\\])*")?\)| ?\[[^\]\n]*\])/,inside:{variable:{pattern:/(!?\[)[^\]]+(?=\]$)/,lookbehind:!0},string:{pattern:/"(?:\\.|[^"\\])*"(?=\)$)/}}}}),Prism.languages.markdown.bold.inside.url=Prism.languages.markdown.url,Prism.languages.markdown.italic.inside.url=Prism.languages.markdown.url,Prism.languages.markdown.bold.inside.italic=Prism.languages.markdown.italic,Prism.languages.markdown.italic.inside.bold=Prism.languages.markdown.bold,function(r){r.languages.php=r.languages.extend("clike",{keyword:/\b(?:and|or|xor|array|as|break|case|cfunction|class|const|continue|declare|default|die|do|else|elseif|enddeclare|endfor|endforeach|endif|endswitch|endwhile|extends|for|foreach|function|include|include_once|global|if|new|return|static|switch|use|require|require_once|var|while|abstract|interface|public|implements|private|protected|parent|throw|null|echo|print|trait|namespace|final|yield|goto|instanceof|finally|try|catch)\b/i,constant:/\b[A-Z0-9_]{2,}\b/,comment:{pattern:/(^|[^\\])(?:\/\*[\s\S]*?\*\/|\/\/.*)/,lookbehind:!0}}),r.languages.insertBefore("php","string",{"shell-comment":{pattern:/(^|[^\\])#.*/,lookbehind:!0,alias:"comment"}}),r.languages.insertBefore("php","keyword",{delimiter:{pattern:/\?>|<\?(?:php|=)?/i,alias:"important"},variable:/\$+(?:\w+\b|(?={))/i,package:{pattern:/(\\|namespace\s+|use\s+)[\w\\]+/,lookbehind:!0,inside:{punctuation:/\\/}}}),r.languages.insertBefore("php","operator",{property:{pattern:/(->)[\w]+/,lookbehind:!0}}),r.languages.insertBefore("php","string",{"nowdoc-string":{pattern:/<<<'([^']+)'(?:\r\n?|\n)(?:.*(?:\r\n?|\n))*?\1;/,greedy:!0,alias:"string",inside:{delimiter:{pattern:/^<<<'[^']+'|[a-z_]\w*;$/i,alias:"symbol",inside:{punctuation:/^<<<'?|[';]$/}}}},"heredoc-string":{pattern:/<<<(?:"([^"]+)"(?:\r\n?|\n)(?:.*(?:\r\n?|\n))*?\1;|([a-z_]\w*)(?:\r\n?|\n)(?:.*(?:\r\n?|\n))*?\2;)/i,greedy:!0,alias:"string",inside:{delimiter:{pattern:/^<<<(?:"[^"]+"|[a-z_]\w*)|[a-z_]\w*;$/i,alias:"symbol",inside:{punctuation:/^<<<"?|[";]$/}},interpolation:null}},"single-quoted-string":{pattern:/'(?:\\[\s\S]|[^\\'])*'/,greedy:!0,alias:"string"},"double-quoted-string":{pattern:/"(?:\\[\s\S]|[^\\"])*"/,greedy:!0,alias:"string",inside:{interpolation:null}}}),delete r.languages.php.string;var e={pattern:/{\$(?:{(?:{[^{}]+}|[^{}]+)}|[^{}])+}|(^|[^\\{])\$+(?:\w+(?:\[.+?]|->\w+)*)/,lookbehind:!0,inside:{rest:r.languages.php}};r.languages.php["heredoc-string"].inside.interpolation=e,r.languages.php["double-quoted-string"].inside.interpolation=e,r.languages.markup&&(r.hooks.add("before-highlight",function(t){"php"===t.language&&/(?:<\?php|<\?)/gi.test(t.code)&&(t.tokenStack=[],t.backupCode=t.code,t.code=t.code.replace(/(?:<\?php|<\?)[\s\S]*?(?:\?>|$)/gi,function(e){for(var n=t.tokenStack.length;-1!==t.backupCode.indexOf("___PHP"+n+"___");)++n;return t.tokenStack[n]=e,"___PHP"+n+"___"}),t.grammar=r.languages.markup)}),r.hooks.add("before-insert",function(e){"php"===e.language&&e.backupCode&&(e.code=e.backupCode,delete e.backupCode)}),r.hooks.add("after-highlight",function(e){if("php"===e.language&&e.tokenStack){e.grammar=r.languages.php;for(var n=0,t=Object.keys(e.tokenStack);n<t.length;++n){var a=t[n],i=e.tokenStack[a];e.highlightedCode=e.highlightedCode.replace("___PHP"+a+"___",'<span class="token php language-php">'+r.highlight(i,e.grammar,"php").replace(/\$/g,"$$$$")+"</span>")}e.element.innerHTML=e.highlightedCode}}))}(Prism),function(e){var n="(?:\\([^|)]+\\)|\\[[^\\]]+\\]|\\{[^}]+\\})+",t={css:{pattern:/\{[^}]+\}/,inside:{rest:e.languages.css}},"class-id":{pattern:/(\()[^)]+(?=\))/,lookbehind:!0,alias:"attr-value"},lang:{pattern:/(\[)[^\]]+(?=\])/,lookbehind:!0,alias:"attr-value"},punctuation:/[\\\/]\d+|\S/};e.languages.textile=e.languages.extend("markup",{phrase:{pattern:/(^|\r|\n)\S[\s\S]*?(?=$|\r?\n\r?\n|\r\r)/,lookbehind:!0,inside:{"block-tag":{pattern:RegExp("^[a-z]\\w*(?:"+n+"|[<>=()])*\\."),inside:{modifier:{pattern:RegExp("(^[a-z]\\w*)(?:"+n+"|[<>=()])+(?=\\.)"),lookbehind:!0,inside:t},tag:/^[a-z]\w*/,punctuation:/\.$/}},list:{pattern:RegExp("^[*#]+(?:"+n+")?\\s+.+","m"),inside:{modifier:{pattern:RegExp("(^[*#]+)"+n),lookbehind:!0,inside:t},punctuation:/^[*#]+/}},table:{pattern:RegExp("^(?:(?:"+n+"|[<>=()^~])+\\.\\s*)?(?:\\|(?:(?:"+n+"|[<>=()^~_]|[\\\\/]\\d+)+\\.)?[^|]*)+\\|","m"),inside:{modifier:{pattern:RegExp("(^|\\|(?:\\r?\\n|\\r)?)(?:"+n+"|[<>=()^~_]|[\\\\/]\\d+)+(?=\\.)"),lookbehind:!0,inside:t},punctuation:/\||^\./}},inline:{pattern:RegExp("(\\*\\*|__|\\?\\?|[*_%@+\\-^~])(?:"+n+")?.+?\\1"),inside:{bold:{pattern:RegExp("(^(\\*\\*?)(?:"+n+")?).+?(?=\\2)"),lookbehind:!0},italic:{pattern:RegExp("(^(__?)(?:"+n+")?).+?(?=\\2)"),lookbehind:!0},cite:{pattern:RegExp("(^\\?\\?(?:"+n+")?).+?(?=\\?\\?)"),lookbehind:!0,alias:"string"},code:{pattern:RegExp("(^@(?:"+n+")?).+?(?=@)"),lookbehind:!0,alias:"keyword"},inserted:{pattern:RegExp("(^\\+(?:"+n+")?).+?(?=\\+)"),lookbehind:!0},deleted:{pattern:RegExp("(^-(?:"+n+")?).+?(?=-)"),lookbehind:!0},span:{pattern:RegExp("(^%(?:"+n+")?).+?(?=%)"),lookbehind:!0},modifier:{pattern:RegExp("(^\\*\\*|__|\\?\\?|[*_%@+\\-^~])"+n),lookbehind:!0,inside:t},punctuation:/[*_%?@+\-^~]+/}},"link-ref":{pattern:/^\[[^\]]+\]\S+$/m,inside:{string:{pattern:/(\[)[^\]]+(?=\])/,lookbehind:!0},url:{pattern:/(\])\S+$/,lookbehind:!0},punctuation:/[\[\]]/}},link:{pattern:RegExp('"(?:'+n+')?[^"]+":.+?(?=[^\\w/]?(?:\\s|$))'),inside:{text:{pattern:RegExp('(^"(?:'+n+')?)[^"]+(?=")'),lookbehind:!0},modifier:{pattern:RegExp('(^")'+n),lookbehind:!0,inside:t},url:{pattern:/(:).+/,lookbehind:!0},punctuation:/[":]/}},image:{pattern:RegExp("!(?:"+n+"|[<>=()])*[^!\\s()]+(?:\\([^)]+\\))?!(?::.+?(?=[^\\w/]?(?:\\s|$)))?"),inside:{source:{pattern:RegExp("(^!(?:"+n+"|[<>=()])*)[^!\\s()]+(?:\\([^)]+\\))?(?=!)"),lookbehind:!0,alias:"url"},modifier:{pattern:RegExp("(^!)(?:"+n+"|[<>=()])+"),lookbehind:!0,inside:t},url:{pattern:/(:).+/,lookbehind:!0},punctuation:/[!:]/}},footnote:{pattern:/\b\[\d+\]/,alias:"comment",inside:{punctuation:/\[|\]/}},acronym:{pattern:/\b[A-Z\d]+\([^)]+\)/,inside:{comment:{pattern:/(\()[^)]+(?=\))/,lookbehind:!0},punctuation:/[()]/}},mark:{pattern:/\b\((?:TM|R|C)\)/,alias:"comment",inside:{punctuation:/[()]/}}}}});var a={inline:e.languages.textile.phrase.inside.inline,link:e.languages.textile.phrase.inside.link,image:e.languages.textile.phrase.inside.image,footnote:e.languages.textile.phrase.inside.footnote,acronym:e.languages.textile.phrase.inside.acronym,mark:e.languages.textile.phrase.inside.mark};e.languages.textile.tag.pattern=/<\/?(?!\d)[a-z0-9]+(?:\s+[^\s>\/=]+(?:=(?:("|')(?:\\[\s\S]|(?!\1)[^\\])*\1|[^\s'">=]+))?)*\s*\/?>/i,e.languages.textile.phrase.inside.inline.inside.bold.inside=a,e.languages.textile.phrase.inside.inline.inside.italic.inside=a,e.languages.textile.phrase.inside.inline.inside.inserted.inside=a,e.languages.textile.phrase.inside.inline.inside.deleted.inside=a,e.languages.textile.phrase.inside.inline.inside.span.inside=a,e.languages.textile.phrase.inside.table.inside.inline=a.inline,e.languages.textile.phrase.inside.table.inside.link=a.link,e.languages.textile.phrase.inside.table.inside.image=a.image,e.languages.textile.phrase.inside.table.inside.footnote=a.footnote,e.languages.textile.phrase.inside.table.inside.acronym=a.acronym,e.languages.textile.phrase.inside.table.inside.mark=a.mark}(Prism);