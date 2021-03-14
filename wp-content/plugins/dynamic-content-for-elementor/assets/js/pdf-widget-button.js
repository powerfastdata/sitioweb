function dcePDFButtonOnClick(e) {
    let settings = e.currentTarget.getAttribute('data-settings');
		var url = window.location.href;
		if (url.indexOf('?') > -1){
				url += '&downloadPDF=' + encodeURIComponent(settings);
		} else {
				url += '?downloadPDF=' + encodeURIComponent(settings);
		}
		var a = document.createElement('a');
		a.href = url;
		document.body.appendChild(a);
		a.click();
		// prevents defaults action:
		return false;
}
