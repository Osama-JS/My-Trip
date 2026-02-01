"use strict";
window.addSwitcher = addSwitcher;

function addSwitcher() {
    var switcherHTML = `
    <div class="sidebar-right">
        <div class="bg-overlay"></div>
        <a class="sidebar-right-trigger wave-effect wave-effect-x" href="javascript:void(0);">
            <span><i class="fa fa-cog fa-spin"></i></span>
        </a>
        <div class="sidebar-right-inner">
            <div class="admin-settings">
                <div class="opt-header-logo">
                    <img src="/images/logo.png" alt="" class="logo-abbr">
                    <img src="/images/logo-text.png" alt="" class="brand-title">
                </div>
                <div class="opt-body">
                    <div class="opt-row">
                        <label>Typography</label>
                        <select id="typography" class="form-control show-select">
                            <option value="poppins" selected="selected">Poppins</option>
                            <option value="roboto">Roboto</option>
                            <option value="opensans">Open Sans</option>
                            <option value="helveticaneue">Helvetica Neue</option>
                        </select>
                    </div>
                    <div class="opt-row">
                        <label>Version</label>
                        <select id="theme_version" class="form-control show-select">
                            <option value="light" selected="selected">Light</option>
                            <option value="dark">Dark</option>
                            <option value="transparent">Transparent</option>
                        </select>
                    </div>
                    <div class="opt-row">
                        <label>Layout</label>
                        <select id="theme_layout" class="form-control show-select">
                            <option value="vertical" selected="selected">Vertical</option>
                            <option value="horizontal">Horizontal</option>
                        </select>
                    </div>
                    <div class="opt-row">
                        <label>Header Background</label>
                        <div class="opt-swatches">
                            <input type="radio" name="header_bg" value="color_1" class="filled-in chk-col-primary" id="header_color_1" checked>
                            <label for="header_color_1"></label>
                            <input type="radio" name="header_bg" value="color_2" class="filled-in chk-col-primary" id="header_color_2">
                            <label for="header_color_2"></label>
                            <input type="radio" name="header_bg" value="color_3" class="filled-in chk-col-primary" id="header_color_3">
                            <label for="header_color_3"></label>
                            <input type="radio" name="header_bg" value="color_4" class="filled-in chk-col-primary" id="header_color_4">
                            <label for="header_color_4"></label>
                            <input type="radio" name="header_bg" value="color_5" class="filled-in chk-col-primary" id="header_color_5">
                            <label for="header_color_5"></label>
                            <input type="radio" name="header_bg" value="transparent" class="filled-in chk-col-primary" id="header_transparent">
                            <label for="header_transparent"></label>
                        </div>
                    </div>
                    <div class="opt-row">
                        <label>Navigation Header</label>
                        <div class="opt-swatches">
                            <input type="radio" name="navigation_header" value="color_1" class="filled-in chk-col-primary" id="nav_header_color_1" checked>
                            <label for="nav_header_color_1"></label>
                            <input type="radio" name="navigation_header" value="color_2" class="filled-in chk-col-primary" id="nav_header_color_2">
                            <label for="nav_header_color_2"></label>
                            <input type="radio" name="navigation_header" value="color_3" class="filled-in chk-col-primary" id="nav_header_color_3">
                            <label for="nav_header_color_3"></label>
                             <input type="radio" name="navigation_header" value="image_1" class="filled-in chk-col-primary" id="nav_header_image_1">
                            <label for="nav_header_image_1"></label>
                        </div>
                    </div>
                     <div class="opt-row">
                        <label>Sidebar Background</label>
                        <div class="opt-swatches">
                            <input type="radio" name="sidebar_bg" value="color_1" class="filled-in chk-col-primary" id="sidebar_color_1" checked>
                            <label for="sidebar_color_1"></label>
                            <input type="radio" name="sidebar_bg" value="color_2" class="filled-in chk-col-primary" id="sidebar_color_2">
                            <label for="sidebar_color_2"></label>
                            <input type="radio" name="sidebar_bg" value="color_3" class="filled-in chk-col-primary" id="sidebar_color_3">
                            <label for="sidebar_color_3"></label>
                             <input type="radio" name="sidebar_bg" value="image_1" class="filled-in chk-col-primary" id="sidebar_image_1">
                            <label for="sidebar_image_1"></label>
                        </div>
                    </div>
                    <div class="opt-row">
                        <label>Sidebar Style</label>
                        <select id="sidebar_style" class="form-control show-select">
                            <option value="full" selected="selected">Full</option>
                            <option value="mini">Mini</option>
                            <option value="compact">Compact</option>
                            <option value="modern">Modern</option>
                            <option value="overlay">Overlay</option>
                            <option value="icon-hover">Icon-Hover</option>
                        </select>
                    </div>
                     <div class="opt-row">
                        <label>Sidebar Position</label>
                        <select id="sidebar_position" class="form-control show-select">
                            <option value="fixed" selected="selected">Fixed</option>
                            <option value="static">Static</option>
                        </select>
                    </div>
                    <div class="opt-row">
                        <label>Header Position</label>
                        <select id="header_position" class="form-control show-select">
                            <option value="fixed" selected="selected">Fixed</option>
                            <option value="static">Static</option>
                        </select>
                    </div>
                    <div class="opt-row">
                        <label>Container Layout</label>
                        <select id="container_layout" class="form-control show-select">
                            <option value="wide" selected="selected">Wide</option>
                            <option value="boxed">Boxed</option>
                            <option value="wide-boxed">Wide Boxed</option>
                        </select>
                    </div>
                    <div class="opt-row">
                        <label>Direction</label>
                        <select id="theme_direction" class="form-control show-select">
                            <option value="ltr" selected="selected">LTR</option>
                            <option value="rtl">RTL</option>
                        </select>
                    </div>
                    <div class="opt-row">
                        <label>Primary Color</label>
                        <div class="opt-swatches">
                            <input type="radio" name="primary_bg" value="color_1" class="filled-in chk-col-primary" id="primary_color_1" checked>
                            <label for="primary_color_1"></label>
                            <input type="radio" name="primary_bg" value="color_2" class="filled-in chk-col-primary" id="primary_color_2">
                            <label for="primary_color_2"></label>
                            <input type="radio" name="primary_bg" value="color_3" class="filled-in chk-col-primary" id="primary_color_3">
                            <label for="primary_color_3"></label>
                            <input type="radio" name="primary_bg" value="color_4" class="filled-in chk-col-primary" id="primary_color_4">
                            <label for="primary_color_4"></label>
                            <input type="radio" name="primary_bg" value="color_5" class="filled-in chk-col-primary" id="primary_color_5">
                            <label for="primary_color_5"></label>
                        </div>
                    </div>
                     <div class="opt-row">
                        <a href="javascript:void(0);" onclick="deleteAllCookie()" class="btn btn-danger btn-sm">Delete All Cookie</a>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    if ($(".sidebar-right").length === 0) {
        $("body").append(switcherHTML);
    }
}

$(document).ready(function () {
    if (typeof window.addSwitcher === "function") {
        window.addSwitcher();
    }
});

(function ($) {
    "use strict";
    // addSwitcher is already called above, but we can call it here too if needed, or remove this call if it's redundant.
    // Keeping it safe.
    if ($(".sidebar-right").length === 0) {
        addSwitcher();
    }

    const body = $("body");
    const html = $("html");

    // Click trigger
    $(".sidebar-right-trigger").on("click", function () {
        $(".sidebar-right").toggleClass("show");
    });

    $(".bg-overlay").on("click", function () {
        $(".sidebar-right").removeClass("show");
    });

    //get the DOM elements from right sidebar
    const typographySelect = $("#typography");
    const versionSelect = $("#theme_version");
    const layoutSelect = $("#theme_layout");
    const sidebarStyleSelect = $("#sidebar_style");
    const sidebarPositionSelect = $("#sidebar_position");
    const headerPositionSelect = $("#header_position");
    const containerLayoutSelect = $("#container_layout");
    const themeDirectionSelect = $("#theme_direction");

    //change the theme typography controller
    typographySelect.on("change", function () {
        body.attr("data-typography", this.value);
        setCookie("typography", this.value);
    });

    //change the theme version controller
    versionSelect.on("change", function () {
        body.attr("data-theme-version", this.value);
        setCookie("version", this.value);
    });

    //change the sidebar position controller
    sidebarPositionSelect.on("change", function () {
        this.value === "fixed" &&
        body.attr("data-sidebar-style") === "modern" &&
        body.attr("data-layout") === "vertical"
            ? alert(
                  "Sorry, Modern sidebar layout dosen't support fixed position!",
              )
            : body.attr("data-sidebar-position", this.value);
        setCookie("sidebarPosition", this.value);
    });

    //change the header position controller
    headerPositionSelect.on("change", function () {
        body.attr("data-header-position", this.value);
        setCookie("headerPosition", this.value);
    });

    //change the theme direction (rtl, ltr) controller
    themeDirectionSelect.on("change", function () {
        html.attr("dir", this.value);
        html.attr("class", "");
        html.addClass(this.value);
        body.attr("direction", this.value);
        setCookie("direction", this.value);
    });

    //change the theme layout controller
    layoutSelect.on("change", function () {
        if (body.attr("data-sidebar-style") === "overlay") {
            body.attr("data-sidebar-style", "full");
            body.attr("data-layout", this.value);
            return;
        }

        body.attr("data-layout", this.value);
        setCookie("layout", this.value);
    });

    //change the container layout controller
    containerLayoutSelect.on("change", function () {
        if (this.value === "boxed") {
            if (
                body.attr("data-layout") === "vertical" &&
                body.attr("data-sidebar-style") === "full"
            ) {
                body.attr("data-sidebar-style", "overlay");
                body.attr("data-container", this.value);

                setTimeout(function () {
                    $(window).trigger("resize");
                }, 200);

                return;
            }
        }

        body.attr("data-container", this.value);
        setCookie("containerLayout", this.value);
    });

    //change the sidebar style controller
    sidebarStyleSelect.on("change", function () {
        if (body.attr("data-layout") === "horizontal") {
            if (this.value === "overlay") {
                alert("Sorry! Overlay is not possible in Horizontal layout.");
                return;
            }
        }

        if (body.attr("data-layout") === "vertical") {
            if (
                body.attr("data-container") === "boxed" &&
                this.value === "full"
            ) {
                alert(
                    "Sorry! Full menu is not available in Vertical Boxed layout.",
                );
                return;
            }

            if (
                this.value === "modern" &&
                body.attr("data-sidebar-position") === "fixed"
            ) {
                alert(
                    "Sorry! Modern sidebar layout is not available in the fixed position. Please change the sidebar position into Static.",
                );
                return;
            }
        }

        body.attr("data-sidebar-style", this.value);

        if (body.attr("data-sidebar-style") === "icon-hover") {
            $(".dlabnav").on(
                "hover",
                function () {
                    $("#main-wrapper").addClass("iconhover-toggle");
                },
                function () {
                    $("#main-wrapper").removeClass("iconhover-toggle");
                },
            );
        }

        setCookie("sidebarStyle", this.value);
    });

    //change the nav-header background controller
    $('input[name="navigation_header"]').on("click", function () {
        body.attr("data-nav-headerbg", this.value);
        setCookie("navheaderBg", this.value);
    });

    //change the header background controller
    $('input[name="header_bg"]').on("click", function () {
        body.attr("data-headerbg", this.value);
        setCookie("headerBg", this.value);
    });

    //change the sidebar background controller
    $('input[name="sidebar_bg"]').on("click", function () {
        body.attr("data-sibebarbg", this.value);
        setCookie("sidebarBg", this.value);
    });

    //change the primary color controller
    $('input[name="primary_bg"]').on("click", function () {
        body.attr("data-primary", this.value);
        setCookie("primary", this.value);
    });
})(jQuery);
