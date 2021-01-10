<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
 
namespace orgelman\fundamental\traits {
   trait bottrap {
      private $bottrap_phoneFormat             = true;
      private $bottrap_phoneOnlineFormat             = true;
      
      public function botTrapFormatNumber($bool) {
         if(is_bool($bool)) {
            $this->bottrap_format = $bool;
         }
         return $this->bottrap_format;
      }
      
      // COPY FROM THIS LINE FOR BOTTRAP
      
      function botTrap($input, $btn=false, $copy=false, $copytext = 'Kopierat till urklpp', $title="", $subject="", $fa="", $style="", $onlinecheckandformat = true, $nojs=false) {
         $input            = trim($input);
         $id               = rand(1000000,9999999)."".uniqid(); 
         $str              = '';
         $text             = '';
         $class            = '';
         if(($input!="") && (!$nojs)) {
            if($btn == true) {
               $class = 'btn btn-label btn-block';
            }
            
            if((!filter_var($input, FILTER_VALIDATE_EMAIL) === false) || (strpos($input, '@') !== false)) {
               $type = 'e';
               if($fa=="") {
                  $fa = "at";
               }
               
               if($btn == true) {
                  $class .= ' btn-email';
               }
            } else {
               $input = preg_replace('/[^0-9,;+.]/', '', $input);
               if(($input!='') && (strlen(preg_replace('/[^0-9.]/', '', $input)) > 2)) {
                  $type = 'p';
                  if($fa=="") {
                     $fa = "phone";
                  }

                  if($btn == true) {
                     $class .= ' btn-phone';
                  }
               }
            }
            

            if($subject!="") {
               $subject = "?subject=".addslashes(urlencode($subject));
            }

            if($type == 'e') {
               $email            = strtolower($input);
               $parts["prefix"]  = substr($email, 0,strrpos($email, '@'));
               $parts["domain"]  = substr(substr(strrchr($email, '@'), 1), 0 , (strrpos(substr(strrchr($email, '@'), 1), ".")));
               $parts["top"]     = substr(strrchr($email, '.'), 1);

               $text = $parts["prefix"];
            }
            if($type == 'p') {
               if((isset($this)) && (method_exists($this,'printPhone')) && ($this->bottrap_phoneFormat)) {
                  $phones  = json_decode($this->printPhone($input));
                  $phone   = $phones['0']->plain;
                  $text    = $phones['0']->number;
               } else if(((isset($this)) && ($this->bottrap_phoneFormat) && ($this->$bottrap_phoneOnlineFormat)) || ($onlinecheckandformat)) {
                  $phones  = json_decode(file_get_contents('https://assets.arcwind.se/phones.print.php?input='.urlencode($input)));
                  $phone   = $phones['0']->plain;
                  $text    = $phones['0']->number;
               } else {
                  $phone   = $input;
                  $text    = $input;
               }

            }

            $id = $type.$id;
            if($text != '') {
            ob_start(); ?><span class="spamfreecontact">
         <span class="<?php echo $id; ?>"></span>
         <script>
            var jQueryScriptOutputted<?php echo $id; ?> = false;
            function initJQuery<?php echo $id; ?>() {
               if(typeof(jQuery) == "undefined") {
                  if(!jQueryScriptOutputted<?php echo $id; ?>) {
                     jQueryScriptOutputted<?php echo $id; ?> = true;
                     document.write("<scr" + "ipt type=\'text/javascript\' src=\'//assets.arcwind.se/scripts/jQuery/jquery-3.5.1.min.js\'></scr" + "ipt>");
                  }
                  setTimeout("initJQuery<?php echo $id; ?>()", 50);
               } else {
                  $(function() {
                     $(".<?php echo $id; ?>").html('<span class="fa-fw"></span>');
                     if($('.<?php echo $id; ?> .fa-fw').css('text-align') != 'center') {
                        $('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', 'https://assets.arcwind.se/scripts/fontawesome/css/all.css') );
                        setTimeout("initJQuery<?php echo $id; ?>()", 50);
                     } else {
                        $(".<?php echo $id; ?>").text("<?php echo $text; ?>");
<?php if($type == 'e') { ?>
                        var pre = "<?php echo $parts["prefix"]; ?>",
                            dom = "<?php echo $parts["domain"]; ?>",
                            linktext = pre + "&#64;" + dom + "." + "<?php echo $parts["top"]; ?>",
                            linktextP = pre,
                            linktextD = dom + "." + "<?php echo $parts["top"]; ?>";

                        $( ".<?php echo $id; ?>"   ).html("<"+"a style='cursor:pointer; <?php echo $style; ?>' target='_blank' class='mail <?php echo $class; ?>' mail=" + linktextP + " dom=" + linktextD + "><" + "/a>");

                        $( ".<?php echo $id; ?> a" ).each(function(){
                           <?php if($title != '') { ?>var t= "'<?php echo $title; ?>'";<?php } else { ?>var t= $(this).attr("mail")+"&#64;"+$(this).attr("dom");<?php } ?>
                           $(this).html("<?php if($btn != true) { ?><i class='fal fa-fw fa-<?php echo $fa; ?>'></i>&#32;<?php } else { ?><span></span><?php } ?>" + t)
                        });
<?php } ?>  
<?php if($type == 'p') { ?>
                        $( ".<?php echo $id; ?>"   ).html("<"+"a style='cursor:pointer; <?php echo $style; ?>' target='_blank' class='phone <?php echo $class; ?>' phone='<?php echo $text; ?>\' plain=\'<?php echo $phone; ?>'><" + "/a>");

                        $( ".<?php echo $id; ?> a" ).each(function(){
                           <?php if($title != '') { ?>var t = "'<?php echo $title; ?>'";<?php } else { ?>var t = $(this).attr("phone");<?php } ?>
                           $(this).html("<?php if($btn != true) { ?><i class='fal fa-fw fa-<?php echo $fa; ?>'></i>&#32;<?php } else { ?><span></span><?php } ?>" + t)
                        });
<?php } ?>  
                        
                        
                        $(".<?php echo $id; ?> a").on("contextmenu", function(e) {
                           e.preventDefault();
                           <?php if($type == 'e') { ?>var t1 = $(this).attr("mail")+'@'+$(this).attr("dom");<?php } if($type == 'p') { ?>var t1 = $(this).attr("phone");<?php } ?>
                           copyTextToClipboard<?php echo $id; ?>(t1, '<?php echo $copytext; ?>')
                        });
                        $(".<?php echo $id; ?> a").on("mousedown", function(e) {
                           e.preventDefault();
                           <?php if($type == 'e') { ?>var t1 = $(this).attr("mail")+'@'+$(this).attr("dom");<?php } if($type == 'p') { ?>var t1 = $(this).attr("phone");<?php } ?>
                           <?php if($type == 'e') { ?>var t2 = "mail"+"to:"+$(this).attr("mail")+'@'+$(this).attr("dom")+"<?php echo $subject; ?>";<?php } if($type == 'p') { ?>var t2 = "te"+"l:"+$(this).attr("plain");<?php } ?>
                           
                           if((e.which == 3) || ('<?php echo $copy; ?>' == true)) {
                              copyTextToClipboard<?php echo $id; ?>(t1, '<?php echo $copytext; ?>')
                           } else {
                              location.href = t2;
                           }
                        });
                     }
                     
                  });
               }
            }
            function copyTextToClipboard<?php echo $id; ?>(text, header) {
               if($('body').data('copied') != text) {
                  const el = document.createElement('textarea');
                  el.value = text;
                  el.setAttribute('readonly', '');
                  el.style.position = 'absolute';
                  el.style.left = '-9999px';
                  document.body.appendChild(el);
                  el.select();
                  document.execCommand('copy');
                  document.body.removeChild(el);

                  if(header == '') {
                     header = 'Copied to clipboard';
                  }

                  $('body').data('copied', text);

                  if(typeof($.gritter) == "undefined") {
                     $('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', 'https://assets.arcwind.se/scripts/Gritter/css/jquery.gritter.css') );
                     $.getScript('https://assets.arcwind.se/scripts/Gritter/js/jquery.gritter.js', function( data, textStatus, jqxhr ) {
                        $.gritter.add({title: '&#10004; ' + header,text: text,sticky: false,time: '5000'});
                     });
                  } else {
                     $.gritter.add({title: '&#10004; ' + header,text: text,sticky: false,time: '5000'});
                  }
               }
            }
            initJQuery<?php echo $id; ?>();
         </script>
         <noscript>
          For full functionality of this site it is necessary to enable JavaScript.
          Here are the <a target="_blank" href="https://www.enable-javascript.com/">
          instructions how to enable JavaScript in your web browser</a>.
         </noscript>
      </span>
      <?php $str = ob_get_clean();
            }
         }
         
         if($str == '') {
            $str = $input;
         }
         
         if($str == '') {
            return false;
         }
         return $str;
      }

      // COPY TO THIS LINE FOR BOTTRAP
      
   }
}
