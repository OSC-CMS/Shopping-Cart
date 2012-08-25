this.vtip = function () {
	this.xOffset = -10;
	this.yOffset = 10;
	$(".vtip").unbind().hover(function (a) {
		this.t = this.title;
		this.title = "";
		this.top = (a.pageY + yOffset);
		this.left = (a.pageX + xOffset);
		$("body").append('<p id="vtip">' + this.t + "</p>");
		$("p#vtip #vtipArrow").attr("src", "vtip_arrow.png");
		$("p#vtip").css("top", this.top + "px").css("left", this.left + "px").fadeIn("slow")
	}, function () {
		this.title = this.t;
		$("p#vtip").fadeOut("slow").remove()
	}).mousemove(function (a) {
		this.top = (a.pageY + yOffset);
		this.left = (a.pageX + xOffset);
		$("p#vtip").css("top", this.top + "px").css("left", this.left + "px")
	})
};
jQuery(document).ready(function (a) {
	vtip()
});