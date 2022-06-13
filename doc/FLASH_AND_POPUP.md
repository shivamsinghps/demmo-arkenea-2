# Fund My Textbook

## Flash messages and popup components description

### Back-end

There are two methods to add flash message. They are located in **FMT\PublicBundle\Controller\AbstractBaseController**:

- *AbstractBaseController::addFlashBagNotice($message, $translate = true, $parameters = [])* - this method add 
success messages
    - `arguments:`
        - $message string - plain text or translation alias
        - $translate boolean - flag for translation tru/false
        - $parameters array - parameters array for translation
   
- *AbstractBaseController::addFlashBagNotice($message, $translate = true, $parameters = [])* - this one add error message
    - `arguments:` 
        - $message string - plain text or translation alias
        - $translate boolean - flag for translation tru/false
        - $parameters array - parameters array for translation
### Front-end
Fron-end part consist of components and HTML templates. They are included in *FMT/PublicBundle/Resources/views/base.html.twig*
There are two components to show flash messages and popups and handler for them: 

- *FMT/PublicBundle/Resources/public/js/util/flash.js* - flash component:
    - `options:`
        - type - type of flash message 
        - message - message 
        - autohide - flag for auto hide message true/false
        - template - jQuery selector for message template
        - targetElement - jQuery selector for parent element for pasting flash messages
        - iconBlock - jQuery selector for icons block template
        
    - `message types:`
        - error
        - info
        - warning
        - success
    
    - `example call:`
    ```javascript
        $('[data-toggle="auto-flash"]').each(function () {
            var type = $(this).data("type"),
                options = {
                    "type": type || $.fmt.flash.FLASH_TYPE_SUCCESS,
                    "message": $(this).html(),
                };
    
            if (type == $.fmt.flash.FLASH_TYPE_ERROR) {
                options.autohide = false;
            }
    
            $.fmt.flash.addFlash(options);
        });
    ```
    
- *FMT/PublicBundle/Resources/public/js/util/popup.js* - popup component:
    - `options:`
        - type - popup's type
        - title - popup's title
        - message - popup's message
        - buttons - popup's custom buttons (object):
            - key - button text 
            - className - button CSS class
            - callback - callback function with popup as a first argument
            
            `example:`
            ```
                buttons: {
                  "Button1": {
                      'className': 'btn btn-default pull-left',
                      'callback': function (modal) {
                          modal.hide();
                      }
                  },
                  "Button12": {
                      'className': 'btn btn-primary',
                      'callback': function (modal) {
                          console.log(modal);
                      }
                  },
                }
            ```
        - template - jQuery selector for popup template
        
         - `example call:`
            ```javascript
                $.fmt.popup.showPopup({
                    title: 'Popup Title',
                    message: 'Popup message',
                    buttons: {
                        "Test": {
                            'className': 'btn btn-default pull-left',
                            'callback': function (modal) {
                                modal.hide();
                            }
                        },
                        "Test2": {
                            'className': 'btn btn-primary',
                            'callback': function (modal) {
                                console.log(modal);
                            }
                        },
                    }
                });
            ```
- *FMT/PublicBundle/Resources/public/js/handler/auto-flash.js* - handler for flash messages component
- *FMT/PublicBundle/Resources/views/common/_flash_messages.htm.twig* - template for flash message
- *FMT/PublicBundle/Resources/views/common/_modal_html.twig* - template for popup
