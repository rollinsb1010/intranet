
/*Jssor*/
(function(g,f,b,d,c,e,A){/*! Jssor */
$Jssor$=g.$Jssor$=g.$Jssor$||{};new(function(){});var m=function(){var b=this,a={};b.S=b.addEventListener=function(b,c){if(typeof c!="function")return;if(!a[b])a[b]=[];a[b].push(c)};b.removeEventListener=function(e,d){var b=a[e];if(typeof d!="function")return;else if(!b)return;for(var c=0;c<b.length;c++)if(d==b[c]){b.splice(c,1);return}};b.d=function(e){var c=a[e],d=[];if(!c)return;for(var b=1;b<arguments.length;b++)d.push(arguments[b]);for(var b=0;b<c.length;b++)try{c[b].apply(g,d)}catch(f){}}},h;(function(){h=function(a,b){this.x=typeof a=="number"?a:0;this.y=typeof b=="number"?b:0};})();var l={de:function(a){return a},ce:function(a){return-b.cos(a*b.PI)/2+.5},Vd:function(a){return-a*(a-2)}},r={ae:37,Zd:39},n={Ud:0,Wd:1,Yd:2,Xd:3,ke:4,le:5},z=1,v=2,w=4,y=5,j,a=new function(){var i=this,m=n.Ud,j=0,q=0,t=0,cb=navigator.appName,k=navigator.userAgent;function D(){if(!m)if(cb=="Microsoft Internet Explorer"&&!!g.attachEvent&&!!g.ActiveXObject){var d=k.indexOf("MSIE");m=n.Wd;q=parseFloat(k.substring(d+5,k.indexOf(";",d)));/*@cc_on@*/j=f.documentMode||q}else if(cb=="Netscape"&&!!g.addEventListener){var c=k.indexOf("Firefox"),a=k.indexOf("Safari"),h=k.indexOf("Chrome"),b=k.indexOf("AppleWebKit");if(c>=0){m=n.Yd;j=parseFloat(k.substring(c+8))}else if(a>=0){var i=k.substring(0,a).lastIndexOf("/");m=h>=0?n.ke:n.Xd;j=parseFloat(k.substring(i+1,a))}if(b>=0)t=parseFloat(k.substring(b+12))}else{var e=/(opera)(?:.*version|)[ \/]([\w.]+)/i.exec(k);if(e){m=n.le;j=parseFloat(e[2])}}}function l(){D();return m==z}function G(){return l()&&(j<6||f.compatMode=="BackCompat")}function V(){D();return m==v}function hb(){D();return m==w}function ib(){D();return m==y}function s(){return l()&&j<9}var B;function r(a){if(!B){p(["transform","WebkitTransform","msTransform","MozTransform","OTransform"],function(b){if(!i.cc(a.style[b])){B=b;return c}});B=B||"transform"}return B}function ab(a){return Object.prototype.toString.call(a)}var J;function p(a,c){if(ab(a)=="[object Array]"){for(var b=0;b<a.length;b++)if(c(a[b],b,a))break}else for(var d in a)if(c(a[d],d,a))break}function jb(){if(!J){J={};p(["Boolean","Number","String","Function","Array","Date","RegExp","Object"],function(a){J["[object "+a+"]"]=a.toLowerCase()})}return J}function u(a){return a==d?String(a):jb()[ab(a)]||"object"}function bb(b,a){setTimeout(b,a||0)}function I(b,d,c){var a=!b||b=="inherit"?"":b;p(d,function(c){var b=c.exec(a);if(b){var d=a.substr(0,b.index),e=a.substr(b.lastIndex+1,a.length-(b.lastIndex+1));a=d+e}});a=c+(a.indexOf(" ")!=0?" ":"")+a;return a}function W(b,a){if(j<9)b.style.filter=a}i.Gb=l;i.jc=hb;i.nc=ib;i.qb=s;i.eb=function(){return j};i.lc=function(){return t};i.Q=bb;i.O=function(a){if(i.he(a))a=f.getElementById(a);return a};i.vb=function(a){return a?a:g.event};i.ec=function(a){a=i.vb(a);var b=new h;if(a.type=="DOMMouseScroll"&&V()&&j<3){b.x=a.screenX;b.y=a.screenY}else if(typeof a.pageX=="number"){b.x=a.pageX;b.y=a.pageY}else if(typeof a.clientX=="number"){b.x=a.clientX+f.body.scrollLeft+f.documentElement.scrollLeft;b.y=a.clientY+f.body.scrollTop+f.documentElement.scrollTop}return b};i.wb=function(c,a,f){if(l()&&q<9){var h=c.style.filter||"",i=new RegExp(/[\s]*alpha\([^\)]*\)/g),e=b.round(100*a),d="";if(e<100||f)d="alpha(opacity="+e+") ";var g=I(h,[i],d);W(c,g)}else c.style.opacity=a==1?"":b.round(a*100)/100};i.ie=function(b,c){var a=r(b);if(a)b.style[a+"Origin"]=c};i.ge=function(a,c){if(l()&&q<9||q<10&&G())a.style.zoom=c==1?"":c;else{var b=r(a);if(b){var f="scale("+c+")",e=a.style[b],g=new RegExp(/[\s]*scale\(.*?\)/g),d=I(e,[g],f);a.style[b]=d}}};i.ee=function(a){if(!a.style[r(a)]||a.style[r(a)]=="none")a.style[r(a)]="perspective(2000px)"};i.e=function(a,c,d,b){a=i.O(a);if(a.addEventListener){c=="mousewheel"&&a.addEventListener("DOMMouseScroll",d,b);a.addEventListener(c,d,b)}else if(a.attachEvent){a.attachEvent("on"+c,d);b&&a.setCapture&&a.setCapture()}};i.ne=function(a,c,d,b){a=i.O(a);if(a.removeEventListener){c=="mousewheel"&&a.removeEventListener("DOMMouseScroll",d,b);a.removeEventListener(c,d,b)}else if(a.detachEvent){a.detachEvent("on"+c,d);b&&a.releaseCapture&&a.releaseCapture()}};i.je=function(b,a){i.e(s()?f:g,"mouseup",b,a)};i.P=function(a){a=i.vb(a);a.preventDefault&&a.preventDefault();a.cancel=c;a.returnValue=e};i.M=function(e,d){for(var b=[],a=2;a<arguments.length;a++)b.push(arguments[a]);var c=function(){for(var c=b.concat([]),a=0;a<arguments.length;a++)c.push(arguments[a]);return d.apply(e,c)};return c};i.be=function(a){a.innerHTML=""};i.nb=function(c){for(var b=[],a=c.firstChild;a;a=a.nextSibling)a.nodeType==1&&b.push(a);return b};function N(a,c,b,f){if(!b)b="u";for(a=a?a.firstChild:d;a;a=a.nextSibling)if(a.nodeType==1){if(a.getAttribute(b)==c)return a;if(f){var e=N(a,c,b,f);if(e)return e}}}i.v=N;function S(a,c,e){for(a=a?a.firstChild:d;a;a=a.nextSibling)if(a.nodeType==1){if(a.tagName==c)return a;if(e){var b=S(a,c,e);if(b)return b}}}i.oe=S;i.re=function(b,a){return b.getElementsByTagName(a)};i.m=function(c){for(var b=1;b<arguments.length;b++){var a=arguments[b];if(a)for(var d in a)c[d]=a[d]}return c};i.cc=function(a){return u(a)=="undefined"};i.se=function(a){return u(a)=="function"};i.he=function(a){return u(a)=="string"};i.pe=function(a){return!isNaN(parseFloat(a))&&isFinite(a)};i.f=p;i.ab=function(a){return i.qe("DIV",a)};i.qe=function(b,a){a=a||f;return a.createElement(b)};i.H=function(){};i.Fb=function(a,b){return a.getAttribute(b)};i.ed=function(b,c,a){b.setAttribute(c,a)};i.Tb=function(a){return a.className};i.mc=function(b,a){b.className=a?a:""};i.cd=function(a){return a.style.display};i.K=function(b,a){b.style.display=a||""};i.ob=function(b,a){b.style.overflow=a};i.ic=function(a){return a.parentNode};i.u=function(a){i.K(a,"none")};i.t=function(a,b){i.K(a,b==e?"none":"")};i.l=function(b,a){b.style.position=a};i.r=function(a,b){a.style.top=b+"px"};i.q=function(a,b){a.style.left=b+"px"};i.E=function(a){return parseInt(a.style.width,10)};i.L=function(c,a){c.style.width=b.max(a,0)+"px"};i.J=function(a){return parseInt(a.style.height,10)};i.I=function(c,a){c.style.height=b.max(a,0)+"px"};i.Ub=function(a){return a.style.cssText};i.Ab=function(b,a){b.style.cssText=a};i.Xb=function(b,a){b.removeAttribute(a)};i.fe=function(b,a){b.style.marginLeft=a+"px"};i.me=function(b,a){b.style.marginTop=a+"px"};i.ac=function(a){return parseInt(a.style.zIndex)||0};i.T=function(c,a){c.style.zIndex=b.ceil(a)};i.Wb=function(b,a){b.style.backgroundColor=a};i.Pc=function(){return l()&&j<10};i.Oc=function(d,c){if(c)d.style.clip="rect("+b.round(c.a)+"px "+b.round(c.h)+"px "+b.round(c.g)+"px "+b.round(c.b)+"px)";else{var g=i.Ub(d),f=[new RegExp(/[\s]*clip: rect\(.*?\)[;]?/i),new RegExp(/[\s]*cliptop: .*?[;]?/i),new RegExp(/[\s]*clipright: .*?[;]?/i),new RegExp(/[\s]*clipbottom: .*?[;]?/i),new RegExp(/[\s]*clipleft: .*?[;]?/i)],e=I(g,f,"");a.Ab(d,e)}};i.p=function(){return+new Date};i.n=function(b,a){b.appendChild(a)};i.Fd=function(b,a){p(a,function(a){i.n(b,a)})};i.bb=function(c,b,a){c.insertBefore(b,a)};i.V=function(b,a){b.removeChild(a)};i.Ed=function(b,a){p(a,function(a){i.V(b,a)})};i.Ad=function(a){i.Ed(a,i.nb(a))};i.gc=function(b,a){var c=f.body;while(a&&b!=a&&c!=a)try{a=a.parentNode}catch(d){return e}return b==a};i.j=function(b,a){return b.cloneNode(a)};function L(b,a,c){a.onload=d;a.abort=d;b&&b(a,c)}i.N=function(e,b){if(i.nc()&&j<11.6||!e)L(b,d);else{var a=new Image;a.onload=i.M(d,L,b,a);a.onabort=i.M(d,L,b,a,c);a.src=e}};i.Td=function(e,b,f){var d=e.length+1;function c(a){d--;if(b&&a&&a.src==b.src)b=a;!d&&f&&f(b)}a.f(e,function(b){a.N(b.src,c)});c()};i.Pd=function(d,k,j,i){if(i)d=a.j(d,c);for(var h=a.re(d,k),f=h.length-1;f>-1;f--){var b=h[f],e=a.j(j,c);a.mc(e,a.Tb(b));a.Ab(e,a.Ub(b));var g=a.ic(b);a.bb(g,e,b);a.V(g,b)}return d};var C;function lb(b){var g=this,h,d,j;function f(){var c=h;if(d)c+="dn";else if(j)c+="av";a.mc(b,c)}function k(){C.push(g);d=c;f()}g.Md=function(){d=e;f()};g.Ld=function(a){j=a;f()};b=i.O(b);if(!C){i.je(function(){var a=C;C=[];p(a,function(a){a.Md()})});C=[]}h=i.Tb(b);a.e(b,"mousedown",k)}i.Nd=function(a){return new lb(a)};var F={s:i.wb,a:i.r,b:i.q,kb:i.L,mb:i.I,od:i.K,c:i.Oc,Ae:i.fe,ze:i.me,F:i.l,lb:i.T};function H(){return F}i.zd=H;i.z=function(c,b){var a=H();p(b,function(d,b){a[b]&&a[b](c,d)})};new(function(){})};j=function(m,r,g,O,C,y){m=m||0;var f=this,q,n,o,x,z=0,B,M,L,D,j=0,t=0,E,k=m,i,h,p,u=[],A;function I(b){i+=b;h+=b;k+=b;j+=b;t+=b;a.f(u,function(a){a,a.Eb(b)})}function N(a,b){var c=a-i+m*b;I(c);return h}function w(w,G){var m=w;if(p&&(m>=h||m<=i))m=((m-i)%p+p)%p+i;if(!E||x||G||j!=m){var o=b.min(m,h);o=b.max(o,i);if(!E||x||G||o!=t){if(y){var s=(o-k)/r;if(g.td&&a.jc())s=b.round(s*r/16)/r*16;if(g.Vb)s=1-s;var e={};for(var n in y){var R=M[n]||1,J=L[n]||[0,1],l=(s-J[0])/J[1];l=b.min(b.max(l,0),1);l=l*R;var H=b.floor(l);if(l!=H)l-=H;var Q=B[n]||B.fb,I=Q(l),q,K=C[n],F=y[n];if(a.pe(F))q=K+(F-K)*I;else{q=a.m({x:{}},C[n]);a.f(F.x,function(c,b){var a=c*I;q.x[b]=a;q[b]+=a})}e[n]=q}if(C.k);if(y.c&&g.xb){var v=e.c.x,D=(v.a||0)+(v.g||0),z=(v.b||0)+(v.h||0);e.b=(e.b||0)+z;e.a=(e.a||0)+D;e.c.b-=z;e.c.h-=z;e.c.a-=D;e.c.g-=D}if(e.c&&a.Pc()&&!e.c.a&&!e.c.b&&e.c.h==g.Pb&&e.c.g==g.Jb)e.c=d;a.f(e,function(b,a){A[a]&&A[a](O,b)})}f.zb(t-k,o-k)}t=o;a.f(u,function(b,c){var a=w<j?u[u.length-c-1]:b;a.A(w,G)});var P=j,N=w;j=m;E=c;f.hb(P,N)}}function F(a,c){c&&a.Zb(h,1);h=b.max(h,a.R());u.push(a)}function H(){if(q){var d=a.p(),e=b.min(d-z,a.nc()?80:20),c=j+e*o;z=d;if(c*o>=n*o)c=n;w(c);if(!x&&c*o>=n*o)J(D);else a.Q(H,g.Cc)}}function v(d,e,g){if(!q){q=c;x=g;D=e;d=b.max(d,i);d=b.min(d,h);n=d;o=n<j?-1:1;f.Ec();z=a.p();H()}}function J(a){if(q){x=q=D=e;f.Bc();a&&a()}}f.Gc=function(a,b,c){v(a?j+a:h,b,c)};f.Dc=function(b,a,c){v(b,a,c)};f.w=function(){J()};f.ud=function(a){v(a)};f.C=function(){return j};f.Fc=function(){return n};f.db=function(){return t};f.A=w;f.Hc=function(){w(i,c)};f.xb=function(a){w(j+a)};f.sd=function(){return q};f.wd=function(a){p=a};f.Zb=N;f.Eb=I;f.Cb=function(a){F(a,0)};f.Db=function(a){F(a,1)};f.R=function(){return h};f.hb=a.H;f.Ec=a.H;f.Bc=a.H;f.zb=a.H;f.rb=a.p();g=a.m({Cc:15},g);p=g.Yb;A=a.m({},a.zd(),g.sc);i=k=m;h=m+r;var M=g.tc||{},L=g.oc||{};B=a.m({fb:a.se(g.y)&&g.y||l.ce},g.y)};var q;new function(){;function n(o,Wb){var k=this;function rc(){var a=this;j.call(a,-1e8,2e8);a.Rd=function(){var c=a.db(),d=b.floor(c),f=v(d),e=c-b.floor(c);return{B:f,Kd:d,F:e}};a.hb=function(d,a){var e=b.floor(a);if(e!=a&&a>d)e++;Lb(e,c);k.d(n.Cd,v(a),v(d),a,d)}}function qc(){var b=this;j.call(b,0,0,{Yb:u});a.f(B,function(a){a.wd(u);b.Db(a);a.Eb(rb/Rb)})}function pc(){var a=this,b=Kb.X;j.call(a,-1,2,{y:l.de,sc:{F:Qb},Yb:u},b,{F:1},{F:-1});a.W=b}function ec(o,m){var a=this,f,g,h,l,b;j.call(a,-1e8,2e8);a.Ec=function(){M=c;Q=d;k.d(n.Dd,v(x.C()),x.C())};a.Bc=function(){M=e;l=e;var a=x.Rd();k.d(n.Bd,v(x.C()),x.C());!a.F&&tc(a.Kd,q)};a.hb=function(d,c){var a;if(l)a=b;else{a=g;if(h)a=i.Id(c/h)*(g-f)+f}x.A(a)};a.Y=function(b,d,c,e){f=b;g=d;h=c;x.A(b);a.A(0);a.Dc(c,e)};a.Jd=function(e){l=c;b=e;a.Gc(e,d,c)};a.Hd=function(a){b=a};x=new rc;x.Cb(o);x.Cb(m)}function fc(){var c=this,b=Pb();a.T(b,0);c.X=b;c.jb=function(){a.u(b);a.be(b)}}function oc(p,o){var f=this,s,w,G,x,g,y=[],Z,r,bb,F,W,D,l,t,h;j.call(f,-E,E+1,{});function C(a){w&&w.Nb();s&&s.Nb();ab(p,a);s=new H.o(p,H,1);w=new H.o(p,H);w.Hc();s.Hc()}function db(){s.rb<H.rb&&C()}function M(o,q,m){if(!F){F=c;if(g&&m){var d=m.width,b=m.height,l=d,j=b;if(d&&b&&i.Z){if(i.Z&3){var h=e,p=L/K*b/d;if(i.Z&1)h=p>1;else if(i.Z&2)h=p<1;l=h?d*K/b:L;j=h?K:b*L/d}a.L(g,l);a.I(g,j);a.r(g,(K-j)/2);a.q(g,(L-l)/2)}a.l(g,"absolute");k.d(n.Rc,Ub)}}a.u(q);o&&o(f)}function cb(b,c,d,e){if(e==Q&&q==o&&N)if(!sc){var a=v(b);z.Sd(a,o,c,f,d);c.Kc();U.Zb(a,1);U.A(a);A.Y(b,b,0)}}function eb(b){if(b==Q&&q==o){if(!l){var a=d;if(z)if(z.B==o)a=z.Qd();else z.jb();db();l=new mc(o,a,f.Sc(),f.Tc());l.wc(h)}!l.sd()&&l.Rb()}}function X(e,c){if(e==o){if(e!=c)B[c]&&B[c].Nc();h&&h.Lc();var j=Q=a.p();f.N(a.M(d,eb,j))}else{var g=b.abs(o-e);(!W||g<=i.uc||u-g<=i.uc)&&f.N()}}function fb(){if(q==o&&l){l.w();h&&h.gd();h&&h.hd();l.zc()}}function gb(){q==o&&l&&l.w()}function T(b){if(P)a.P(b);else k.d(n.fd,o,b)}function R(){h=t.pInstance;l&&l.wc(h)}f.N=function(e,b){b=b||x;if(y.length&&!F){a.t(b);if(!bb){bb=c;k.d(n.kd);a.f(y,function(b){if(!b.src){b.src=a.Fb(b,"src2");a.K(b,b["display-origin"])}})}a.Td(y,g,a.M(d,M,e,b))}else M(e,b)};f.ld=function(){if(z){var b=z.pd(u);if(b){var f=Q=a.p(),c=o+1,e=B[v(c)];return e.N(a.M(d,cb,c,e,b,f),x)}}V(q+i.xc)};f.Sb=function(){X(o,o)};f.Nc=function(){h&&h.gd();h&&h.hd();f.qc();l&&l.dd();l=d;C()};f.Kc=function(){a.u(p)};f.qc=function(){a.t(p)};f.Yc=function(){h&&h.Lc()};function ab(b,f,d){d=d||0;if(!D){if(b.tagName=="IMG"){y.push(b);if(!b.src){W=c;b["display-origin"]=a.cd(b);a.u(b)}}a.qb()&&a.T(b,a.ac(b)+1);if(a.lc()>0)(!I||a.lc()<534||!S)&&a.ee(b)}var h=a.nb(b);a.f(h,function(h){var j=a.Fb(h,"u");if(j=="player"&&!t){t=h;if(t.pInstance)R();else a.e(t,"dataavailable",R)}if(j=="caption"){if(!a.Gb()&&!f){var i=a.j(h,c);a.bb(b,i,h);a.V(b,h);h=i;f=c}}else if(!D&&!d&&!g&&a.Fb(h,"u")=="image"){g=h;if(g){if(g.tagName=="A"){Z=g;a.z(Z,O);r=a.j(g,e);a.e(r,"click",T);a.z(r,O);a.K(r,"block");a.wb(r,0);a.Wb(r,"#000");g=a.oe(g,"IMG")}g.border=0;a.z(g,O)}}ab(h,f,d+1)})}f.zb=function(c,b){var a=E-b;Qb(G,a)};f.Sc=function(){return s};f.Tc=function(){return w};f.B=o;m.call(f);var J=a.v(p,"thumb");if(J){f.Vc=a.j(J,c);a.u(J)}a.t(p);x=a.j(Y,c);a.T(x,1e3);a.e(p,"click",T);C(c);D=c;f.vc=g;f.pc=r;f.W=G=p;a.n(G,x);k.S(203,X);k.S(22,gb);k.S(24,fb)}function mc(g,r,v,u){var b=this,m=0,x=0,o,h,d,f,l,s,w,t,p=B[g];j.call(b,0,0);function y(){a.Ad(J);Vb&&l&&p.pc&&a.n(J,p.pc);a.t(J,l||!p.vc)}function A(){if(s){s=e;k.d(n.bd,g,d,m,h,d,f);b.A(h)}b.Rb()}function C(a){t=a;b.w();b.Rb()}b.Rb=function(){var a=b.db();if(!F&&!M&&!t&&(a!=d||N&&(!Nb||fb))&&q==g){if(!a){if(o&&!l){l=c;b.zc(c);k.d(n.ad,g,m,x,o,f)}y()}var e,i=n.rc;if(a==f)return p.ld();else if(a==d)e=f;else if(a==h)e=d;else if(!a)e=h;else if(a>d){s=c;e=d;i=n.Zc}else e=b.Fc();k.d(i,g,a,m,h,d,f);b.Dc(e,A)}};b.dd=function(){z&&z.B==g&&z.jb();var a=b.db();a<f&&k.d(n.rc,g,-a-1,m,h,d,f)};b.zc=function(b){r&&a.ob(bb,b&&r.yc.xe?"":"hidden")};b.zb=function(b,a){if(l&&a>=o){l=e;y();p.qc();z.jb();k.d(n.Wc,g,m,x,o,f)}k.d(n.id,g,a,m,h,d,f)};b.wc=function(a){if(a&&!w){w=a;a.S($JssorPlayer$.xd,C)}};r&&b.Db(r);o=b.R();b.R();b.Db(v);h=v.R();d=h+i.dc;u.Eb(d);b.Cb(u);f=b.R()}function Qb(c,g){var f=w>0?w:i.D,d=b.round(vb*g*(f&1)),e=b.round(wb*g*(f>>1&1));if(a.Gb()&&a.eb()>=10&&a.eb()<11)c.style.msTransform="translate("+d+"px, "+e+"px)";else if(a.jc()&&a.eb()>=30){c.style.WebkitTransition="transform 0s";c.style.WebkitTransform="translate3d("+d+"px, "+e+"px, 0px) perspective(2000px)"}else{a.q(c,d);a.r(c,e)}}function lc(a){P=0;!G&&ic()&&kc(a)}function kc(b){kb=M;F=c;ub=e;Q=d;a.e(f,hb,Sb);a.p();Db=A.Fc();A.w();if(!kb)w=0;if(I){var h=b.touches[0];pb=h.clientX;qb=h.clientY}else{var g=a.ec(b);pb=g.x;qb=g.y;a.P(b)}D=0;X=0;ab=0;C=x.C();k.d(n.Jc,v(C),C,b)}function Sb(d){if(F&&(!a.qb()||d.button)){var e;if(I){var j=d.touches;if(j&&j.length>0)e=new h(j[0].clientX,j[0].clientY)}else e=a.ec(d);if(e){var f=e.x-pb,g=e.y-qb;if(b.floor(C)!=C)w=i.D&G;if((f||g)&&!w){if(G==3)if(b.abs(g)>b.abs(f))w=2;else w=1;else w=G;if(I&&w==1&&b.abs(g)-b.abs(f)>3)ub=c}if(w){var l=g,k=wb;if(w==1){l=f;k=vb}if(D-X<-2)ab=1;else if(D-X>2)ab=0;X=D;D=l;mb=C-D/k/(gb||1);if(D&&w&&!ub){a.P(d);if(!M)A.Jd(mb);else A.Hd(mb)}else a.qb()&&a.P(d)}}}else zb(d)}function zb(h){gc();if(F){F=e;a.p();a.ne(f,hb,Sb);P=D;P&&a.P(h);A.w();var c=x.C();k.d(n.Od,v(c),c,v(C),C,h);var d=b.floor(C);if(b.abs(D)>=i.sb){d=b.floor(c);d+=ab}var g=b.abs(d-c);g=1-b.pow(1-g,5);if(!P&&kb)A.ud(Db);else if(c==d){nb.Yc();nb.Sb()}else A.Y(c,d,g*Mb)}}function dc(a){B[q];q=v(a);nb=B[q];Lb(a);return q}function tc(a,b){w=0;dc(a);k.d(n.yd,v(a),b)}function Lb(b,c){a.f(R,function(a){a.yb(v(b),b,c)})}function ic(){var a=n.Ac||0;n.Ac|=i.i;return G=i.i&~a}function gc(){if(G){n.Ac&=~i.i;G=0}}function Pb(){var b=a.ab();a.z(b,O);a.l(b,"absolute");return b}function v(a){return(a%u+u)%u}function ac(b,a){V(b,i.gb,a)}function tb(){a.f(R,function(a){a.Bb(a.pb.vd>fb)})}function Yb(b){b=a.vb(b);var c=b.target?b.target:b.srcElement,d=b.relatedTarget?b.relatedTarget:b.toElement;if(!a.gc(o,c)||a.gc(o,d))return;fb=1;tb();B[q].Sb()}function Xb(){fb=0;tb()}function Zb(){O={kb:L,mb:K,a:0,b:0};a.f(T,function(b){a.z(b,O);a.l(b,"absolute");a.ob(b,"hidden");a.u(b)});a.z(Y,O)}function db(b,a){V(b,a,c)}function V(h,g,l){if(Jb&&(!F||i.kc)){M=c;F=e;A.w();if(a.cc(g))g=Mb;var f=Ab.db(),d=h;if(l){d=f+h;if(h>0)d=b.ceil(d);else d=b.floor(d)}var k=(d-f)%u;d=f+k;var j=f==d?0:g*b.abs(k);j=b.min(j,g*E*1.5);A.Y(f,d,j)}}k.rd=V;k.Gc=function(){if(!N){N=c;B[q]&&B[q].Sb()}};k.nd=function(){return P};k.md=function(){return a.E(s||o)};k.qd=function(c){if(!S||!a.Gb()||a.eb()>=8){if(!s){var b=a.j(o,e);a.Xb(b,"id");a.l(b,"relative");a.r(b,0);a.q(b,0);s=a.j(o,e);a.Xb(s,"id");a.Ab(s,"");a.l(s,"absolute");a.r(s,0);a.q(s,0);a.L(s,a.E(o));a.I(s,a.J(o));a.ie(s,"0 0");a.n(s,b);var d=a.nb(o);a.n(o,s);a.Fd(b,d);a.t(b);a.t(s)}gb=c/a.E(s);a.ge(s,gb);a.L(o,c);a.I(o,gb*a.J(s))}};k.fc=function(a){var d=b.ceil(v(rb/Rb)),c=v(a-q+d);if(c>E){if(a-q>u/2)a-=u;else if(a-q<=-u/2)a+=u}else a=q+c-d;return a};m.call(this);k.X=o=a.O(o);var i=a.m({Z:0,uc:1,ib:0,tb:e,kc:c,xc:1,dc:3e3,ub:3,gb:500,Id:l.Vd,sb:20,Ib:0,G:1,cb:0,Qb:1,D:1,i:1},Wb),Z=i.ye,H=a.m({o:t},i.we),ob=i.te,jb=i.ue,W=i.Ic,cb=i.Qb,s,y=a.v(o,"slides",d,cb),Y=a.v(o,"loading",d,cb)||a.ab(f),Gb=a.v(o,"navigator",d,cb),Cb=a.v(o,"thumbnavigator",d,cb),cc=a.E(y),bc=a.J(y);if(i.G>1||i.cb)i.i&=i.D;var O,T=a.nb(y),q=-1,nb,u=T.length,L=i.Uc||cc,K=i.Qc||bc,Ob=i.Ib,vb=L+Ob,wb=K+Ob,Rb=i.D==1?vb:wb,E=b.min(i.G,u),bb,w,G,ub,I,R=[],Tb,Eb,Ib,Vb,sc,N,Nb=i.ub,Mb=i.gb,lb,S,rb,Jb=E<u,jc=Jb&&i.i,P,fb=1,M,F,Q,pb=0,qb=0,D,X,ab,Ab,x,U,A,Kb=new fc,gb;N=i.tb;k.pb=Wb;Zb();o["jssor-slider"]=c;a.T(y,a.ac(y));a.l(y,"absolute");bb=a.j(y);a.bb(a.ic(y),bb,y);if(Z){Vb=Z.ve;lb=Z.o;S=E==1&&u>1&&lb}rb=S||E>=u?0:i.cb;var sb=y,B=[],z,J,yb="mousedown",hb="mousemove",Bb="mouseup",eb,C,kb,Db,mb;if(g.navigator.msPointerEnabled){yb="MSPointerDown";hb="MSPointerMove";Bb="MSPointerUp";eb="MSPointerCancel";if(i.i){var xb="none";if(i.i==1)xb="pan-y";else if(i.i==2)xb="pan-x";a.ed(sb.style,"-ms-touch-action",xb)}}else if("ontouchstart"in g||"createTouch"in f){I=c;yb="touchstart";hb="touchmove";Bb="touchend";eb="touchcancel"}U=new pc;if(S)z=new lb(Kb,L,K,Z,I);a.n(bb,U.W);a.ob(y,"hidden");J=Pb();a.Wb(J,"#000");a.wb(J,0);a.bb(sb,J,sb.firstChild);for(var ib=0;ib<T.length;ib++){var nc=T[ib],Ub=new oc(nc,ib);B.push(Ub)}a.u(Y);Ab=new qc;A=new ec(Ab,U);if(jc){a.e(y,yb,lc);a.e(f,Bb,zb);eb&&a.e(f,eb,zb)}Nb&=I?2:1;if(Gb&&ob){Tb=new ob.o(Gb,ob);R.push(Tb)}if(jb){Eb=new jb.o(o,jb,i.Qb);R.push(Eb)}if(Cb&&W){W.ib=i.ib;Ib=new W.o(Cb,W);R.push(Ib)}a.f(R,function(a){a.Ob(u,B,Y);a.S(p.bc,ac)});a.e(o,"mouseout",Yb);a.e(o,"mouseover",Xb);tb();i.Mc&&a.e(f,"keydown",function(a){if(a.keyCode==r.ae)db(-1);else a.keyCode==r.Zd&&db(1)});k.qd(k.md());A.Y(i.ib,i.ib,0)}n.fd=21;n.Jc=22;n.Od=23;n.Dd=24;n.Bd=25;n.kd=26;n.Rc=27;n.Cd=202;n.yd=203;n.ad=206;n.Wc=207;n.id=208;n.rc=209;n.Zc=210;n.bd=211;q=n};var p={bc:1};var s=function(i,A){var h=this,x,l,d,u=[],y,w,f,n,o,t,s,k,r,g,j;m.call(h);i=a.O(i);function z(n,e){var g=this,b,m,k;function o(){m.Ld(l==e)}function i(){if(!r.nd()){var a=(f-e%f)%f,b=r.fc((e+a)/f),c=b*f-a;h.d(p.bc,c)}}g.B=e;g.hc=o;k=n.Vc||n.vc||a.ab();g.W=b=a.Pd(j,"ThumbnailTemplate",k,c);m=a.Nd(b);d.Kb&1&&a.e(b,"click",i);d.Kb&2&&a.e(b,"mouseover",i)}h.yb=function(c,d,e){var a=l;l=c;a!=-1&&u[a].hc();u[c].hc();!e&&r.rd(r.fc(b.floor(d/f)))};h.Bb=function(b){a.t(i,b)};var v;h.Ob=function(F,D){if(!v){x=F;b.ceil(x/f);l=-1;k=b.min(k,D.length);var h=d.U&1,p=t+(t+n)*(f-1)*(1-h),m=s+(s+o)*(f-1)*h,C=p+(p+n)*(k-1)*h,A=m+(m+o)*(k-1)*(1-h);a.l(g,"absolute");a.ob(g,"hidden");d.Hb&1&&a.q(g,(y-C)/2);d.Hb&2&&a.r(g,(w-A)/2);a.L(g,C);a.I(g,A);var j=[];a.f(D,function(l,e){var i=new z(l,e),d=i.W,c=b.floor(e/f),k=e%f;a.q(d,(t+n)*k*(1-h));a.r(d,(s+o)*k*h);if(!j[c]){j[c]=a.ab();a.n(g,j[c])}a.n(j[c],d);u.push(i)});var E=a.m({tb:e,kc:e,Uc:p,Qc:m,Ib:n*h+o*(1-h),sb:12,gb:200,ub:3,D:d.U,i:d.Xc?0:d.U},d);r=new q(i,E);v=c}};h.pb=d=a.m({Lb:3,Mb:3,G:1,U:1,Hb:3,Kb:1},A);y=a.E(i);w=a.J(i);g=a.v(i,"slides");j=a.v(g,"prototype");a.V(g,j);f=d.jd||1;n=d.Lb;o=d.Mb;t=a.E(j);s=a.J(j);k=d.G};function t(){j.call(this,0,0);this.Nb=a.H}jssor_slider1_starter=function(a){new q(a,{tb:c,xc:1,dc:4e3,ub:0,Mc:c,gb:500,sb:20,Ib:5,G:1,cb:0,Qb:1,D:1,i:3,Ic:{o:s,vd:2,Hb:3,jd:1,Lb:4,Mb:4,G:4,cb:0,U:2,Xc:e}})}})(window,document,Math,null,true,false)