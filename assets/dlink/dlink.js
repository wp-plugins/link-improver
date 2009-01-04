/******************* -DLINK- Link sexer *******************\
             Courtesy of http://oopstudios.com/
 Loops through all the links on a page and creates extra
  usability by adding icons representing their content:
   * Plain webpages (favicon)
   * Direct file pointers
   * Mailto links and Anchors
 Each link will have it's class modified to include on of 
  the following for your styling needs:
   * internal
   * subdomain
   * external
   * email
 Finally, you can append certain variables when including
  this script to modify its behaviour:
   * notargets
   * notitles
   * nomailevents
     for example:
 http://oopstudios.com/dlink/dlink.js?notargets&notitles&nomailevents
\**********************************************************/

DLINK = {};
// Create the prefs object
DLINK.prefs = {};
// Path to self
s = document.getElementsByTagName ("script");
for (var i=0, n=s.length; i<n; i++) {
  if (s[i].src.match ("dlink.js")) {
    // Index
    var n = s[i].src.lastIndexOf ("dlink.js");
    // Base path
    DLINK.path = s[i].src.substring (0, n);
    // Have we passed any vars?
    var v = s[i].src.substring (n).split ("?");
    if (v.length > 1) {
      // Add the prefs
      v = v[1].split ("&");
      for (p=0; p<v.length; p++) {
        DLINK.prefs[v[p]] = true;
      }
    }
    // Done
    break;
  }
}
//
// A list of the file-extensions we look for, the left side represents
//  the icon, and the right are all that it applies to.
//
DLINK.exts = {
  web: ["php", "htm", "html", "asp", "aspx", "cfm"],
  img: ["png", "gif", "jpg", "jpeg", "svg", "eps"],
  doc: ["doc", "odt", "rtf"],
  pdf: ["pdf"],
  swf: ["swf"],
  fla: ["fla"],
  txt: ["txt", "js", "css", "inc"],
  zip: ["zip", "tar", "rar", "7z", "gzip", "bzip", "ace"],
  aud: ["mp3", "flac", "aac", "wma", "wav", "ogg"],
  vid: ["flv", "avi", "mov", "mp4", "mpg", "mpeg", "wmv"],
  rss: ["rss", "atom"],
  tor: ["torrent"]
};
//
// Regex's for analyzing link paths
//
DLINK.regex = {
  subd: new RegExp ("\\." + location.host + "$"), // Checks if subdomain
  extn: new RegExp ("\\.([^\\.]{2,7})$"),         // Returns the extension
  mail: new RegExp ("^mailto:(.*)$")              // Gets an email addy
};
//
// Used for email links
//
DLINK.doMail = function (link) {
  var tmp = unescape (link.mail);
  tmp = tmp.split ("?");
  tmp = tmp[0];
  var q = confirm ("This is an email link to:\n\n" + tmp + "\n\nclick 'OK' to open your default email program.");
  return q;
}
//
// Iconizes all the links on the page
//
DLINK.hasInit = false;
DLINK.init = function () {
  // have we already initialized?
  if (DLINK.hasInit) {
    return;
  }
  DLINK.hasInit = true;
  // Highlight all appropriate links
  var dlinks = getElementsByClassName ("dlink");
  for (var d=0; d<dlinks.length; d++) {
    DLINK.highlight (dlinks[d]);
  }
};
//
// Iconizes an element
//  feel free to call this seperately
//
DLINK.highlight = function (el) {
  var links = el.getElementsByTagName ("a");
  // Check through all the links
  for (var l=0; l<links.length; l++) {
    // Assume some vars
    var link = links[l];
    var rela = "internal";
    var icon = "web";
    var scur = false;
    // Skip the link?
    if (link.className) {
      var clss = link.className.toString ();
      if (clss.search (/no_dlink/) != -1) {
        continue;
      }
    }
    if (!link.href) {
      continue;
    }
    // Figure out the link relativity and "non-webby" icons
    switch (link.protocol.toLowerCase ()) {
      // EMail links first
      case "mailto:":
        // Set the properties
        rela = "email";
        icon = "eml";
        // Add an onclick event for extra usefulness?
        if (!DLINK.prefs["nomailevents"] && !link.onclick) {
          // Get the addy
          var mail = DLINK.regex.mail.exec (link.href);
          if (mail != null) {
            // Add the func
            link.mail = mail[1];
            link.onclick = function () { return DLINK.doMail (this); }
          }
        }
        break;
      // Javascript is "internal" (eww)
      case "javascript:":
        rela = "internal";
        break;
      // HTTP and HTTPS
      case "https:":
        // Secure mode please
        var scur = true;
      case "http:":
        // Internal?
        if (link.hostname == location.hostname || !link.href) {
          rela = "internal";
          // Check for a hash
          if (link.hash) {
            var icon = "anc";
          }
        } else {
          // Subdomain?
          var subd = DLINK.regex.subd.exec (link.hostname);
          if (subd != null) {
            rela = "subdomain";
          } else {
            rela = "external";
          }
        }
        break;
      // All other protocols are "external"
      default:
        rela = "external";
        break;
    }
    // Do we need to find an icon?
    if (icon == "web") {
      // Look for a file extension
      var extn = DLINK.regex.extn.exec (link.pathname);
      if (extn != null) {
        // Loop to find if the icon
        var isDone = false;
        for (n in DLINK.exts) {
          for (o in DLINK.exts[n]) {
            if (DLINK.exts[n][o] == extn[1]) {
              icon = n;
              isDone = true;
              break;
            }
          }
          if (isDone == true) {
            break;
          }
        }
      }
    }
    // Prepend the image
    var myImg = new Image (16, 16);
    myImg.style.padding       = "0 4px 0 0";
    myImg.style.verticalAlign = "middle";
    if (icon == "web" && link.host) {
      myImg.src = link.protocol + "//" + link.host + "/favicon.ico";
    } else {
      myImg.src = DLINK.path + "gfx/" + icon + ".png";
    }
    myImg.onerror = function () {
      this.src = DLINK.path + "gfx/web.png";
    }
    link.parentNode.insertBefore (myImg, link);
    // Secure icon?
    /*
    if (scur) {
      // Overlay the icons
      var myImg2 = new Image (16, 16);
      myImg2.style.padding       = "0 4px 0 0";
      myImg2.style.verticalAlign = "middle";
      myImg2.style.margin        = "0 0 0 -20px";
      myImg2.src = DLINK.path + "gfx/secure.png";
      link.parentNode.insertBefore (myImg2, link);
    }
    */
    // Add the classname
    link.className += " " + rela;
    // External links might need the target set
    if (rela == "external") {
      if (!DLINK.prefs["notargets"] && !link.target) {
        link.target = "_blank";
      }
    }
    // Are we modding the title?
    if (!DLINK.prefs["notitles"]) {
      link.title = (link.title ? link.title + " (" : "(") + rela + ", " + icon + ")";
    }
  }
};
//
// Automatically add the onload event to the page
//
if (window.attachEvent) window.attachEvent ("onload", DLINK.init);
else window.addEventListener ("load", DLINK.init, false);

/***************** getElementsByClassName *****************\
 Developed by Robert Nyman, http://www.robertnyman.com
 Code/licensing: http://code.google.com/p/getelementsbyclassname/
\**********************************************************/

if (!window.getElementsByClassName) {
  var getElementsByClassName = function (className, tag, elm){
    if (document.getElementsByClassName) {
      getElementsByClassName = function (className, tag, elm) {
        elm = elm || document;
        var elements = elm.getElementsByClassName(className),
          nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
          returnElements = [],
          current;
        for(var i=0, il=elements.length; i<il; i+=1){
          current = elements[i];
          if(!nodeName || nodeName.test(current.nodeName)) {
            returnElements.push(current);
          }
        }
        return returnElements;
      };
    }
    else if (document.evaluate) {
      getElementsByClassName = function (className, tag, elm) {
        tag = tag || "*";
        elm = elm || document;
        var classes = className.split(" "),
          classesToCheck = "",
          xhtmlNamespace = "http://www.w3.org/1999/xhtml",
          namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
          returnElements = [],
          elements,
          node;
        for(var j=0, jl=classes.length; j<jl; j+=1){
          classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
        }
        try  {
          elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
        }
        catch (e) {
          elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
        }
        while ((node = elements.iterateNext())) {
          returnElements.push(node);
        }
        return returnElements;
      };
    }
    else {
      getElementsByClassName = function (className, tag, elm) {
        tag = tag || "*";
        elm = elm || document;
        var classes = className.split(" "),
          classesToCheck = [],
          elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
          current,
          returnElements = [],
          match;
        for(var k=0, kl=classes.length; k<kl; k+=1){
          classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
        }
        for(var l=0, ll=elements.length; l<ll; l+=1){
          current = elements[l];
          match = false;
          for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
            match = classesToCheck[m].test(current.className);
            if (!match) {
              break;
            }
          }
          if (match) {
            returnElements.push(current);
          }
        }
        return returnElements;
      };
    }
    return getElementsByClassName(className, tag, elm);
  };
}
