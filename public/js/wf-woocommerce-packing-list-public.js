handlePrintButtonClickedInMyAccoutPage();
function handlePrintButtonClickedInMyAccoutPage() {
	document.addEventListener('DOMContentLoaded', function () {
		var printButtons = document.querySelectorAll('.wt_pklist_invoice_print, .wt_pklist_packinglist_print, .wt_pklist_deliverynote_print, .wt_pklist_dispatchlabel_print, .wt_pklist_shippinglabel_print, .wt_pklist_proformainvoice_print' );
		printButtons.forEach(function (button) {
			button.addEventListener('click', function (e) {
				e.preventDefault();
				var action_url = this.getAttribute('href');
				if ( 'Yes' === wf_pklist_params_public.show_document_preview || ( "logged_in" === wf_pklist_params_public.document_access_type && '' === wf_pklist_params_public.is_user_logged_in ) ) {
					window.open(action_url, '_blank');
				} else if ( window.innerWidth <= 768 ) { // check for the mobile device
					do_print_document_in_myaccount_page_in_mobile_device( action_url );
				} else {
					do_print_document_in_myaccount_page(action_url);
				}
			});
		});
	});
}

function do_print_document_in_myaccount_page_in_mobile_device( url ) {
	var newWindow = window.open(url, '_blank');
	// Once the new window has loaded, trigger the print dialog
	newWindow.onload = function() {
		newWindow.focus();  // Focus the new window before printing
		newWindow.print();

		// Optionally close the window after printing (mobile browsers may block this)
		setTimeout(function() {
			newWindow.close();
		}, 2000);  // Adjust the delay if necessary
	};
}

function do_print_document_in_myaccount_page( url, is_bulk_print = false, reload_page = false ) {
	var newWindow = window.open('', '_blank');
	if (newWindow) {
		newWindow.document.open();
		newWindow.document.write(wf_pklist_params_public.msgs.generating_document_text);
		newWindow.document.close();
		newWindow.document.body.style.cursor = 'progress';
	}
	var xhr = new XMLHttpRequest();
	xhr.open('GET', url, true);
	xhr.onload = function () {
		var responseText = xhr.responseText;
        var contentType = xhr.getResponseHeader("Content-Type");
        if (contentType && contentType.includes("text/plain")) {
            // Close the new window immediately if the content is plain text
            if (newWindow) {
                newWindow.close();
            }
            // Show the alert message after closing the window
            setTimeout(function () {
                alert(responseText);
            }, 100); // A short delay to ensure the window closes before alert
            return;
		}
		
		if (200 === this.status) {
			if (newWindow) {
				// Write an iframe to the new tab
				newWindow.document.open();
				newWindow.document.write('<html><head><title>'+wf_pklist_params_public.msgs.generating_document_text+'</title></head><body><iframe id="printIframe" style="width: 100%; height: 100%; border: none;"></iframe></body></html>');
				newWindow.document.close();
	
				// Get the iframe element
				var printIframe = newWindow.document.getElementById('printIframe');
				printIframe.style.display = 'none';
				// Write the response to the iframe
				var iframeDoc = printIframe.contentDocument || printIframe.contentWindow.document;
				iframeDoc.open();
				iframeDoc.write(xhr.responseText);
				iframeDoc.close();
				
				var iframeTitle = iframeDoc.title || 'Document';
				newWindow.document.title = iframeTitle;

				// Set the title of the new window from the iframe content
				setTimeout(function () {
					printIframe.contentWindow.focus();
					printIframe.contentWindow.print();
					newWindow.document.body.style.cursor = 'auto';

					// Remove the iframe after printing
					newWindow.document.body.removeChild(printIframe);
					newWindow.close();
					if (true === is_bulk_print) {
						// here comes the code for bulk print.
					} else if ( true === reload_page ) {
						window.location.reload(true);
					}
				}, 500);
	
			} else {
				alert(wf_pklist_params_public.msgs.new_tab_open_error);
			}
		} else {
			if (newWindow) {
				newWindow.document.body.style.cursor = 'auto'; // Reset cursor on error
			}
			alert(wf_pklist_params_public.msgs.error_loading_data);
		}
	};

	xhr.onerror = function () {
		if (newWindow) {
			newWindow.document.body.style.cursor = 'auto'; // Reset cursor on request error
		}
		alert(wf_pklist_params_public.msgs.request_error);
		setTimeout(function () { 
			jQuery('.wf_cst_overlay, .wf_pklist_popup').hide();
		},1000);
	};
	xhr.send();
}

function wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(url,a)
{
	/*
	1 - invoice/proforma invoice number
	2 - invoice for free order
	3 - empty from address for invoice
	11 - creditnote number
	
	*/
	if((1 === a || "1" === a) || (2 === a || "2" === a) || (3 === a || "3" === a) || ("11" === a || 11 === a))
	{
		if("2" === a || 2 === a){
			var invoice_prompt = wf_pklist_params_public.msgs.invoice_number_prompt_free_order;
		}else if("11" === a || 11 === a){
			var invoice_prompt = wf_pklist_params_public.msgs.creditnote_number_prompt;
		}else if("3" === a || 3 === a){
			var invoice_prompt = wf_pklist_params_public.msgs.invoice_number_prompt_no_from_addr;
			alert(invoice_prompt);
			return false;
		}else{
			var msg_title=((1 === a || "1" === a) ? wf_pklist_params_public.msgs.invoice_title_prompt : a);
			var invoice_prompt = msg_title+' '+wf_pklist_params_public.msgs.invoice_number_prompt;
		}
		
		if(true === wf_pklist_params_public.msgs.pop_dont_show_again){
			url = url+'&wt_dont_show_again=1';
			window.open(url, '_blank');
			setTimeout(function () {
				window.location.reload(true);
			}, 1000);   
		}else{
			if(confirm (invoice_prompt))
			{       
				window.open(url, '_blank');
				setTimeout(function () {
					window.location.reload(true);
				}, 1000);
			} else {
				return false;
			}
		}
	}
	else
	{
		window.open(url, '_blank');
		setTimeout(function () {
			window.location.reload(true);
		}, 1000);                   
	}
	return false;
}