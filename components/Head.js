import NextHead from 'next/head';

export default class Head extends React.Component {

    render() {
        return (
            <div>
                <NextHead>
                    <title>ICE HR</title>
                    <meta name="keywords" content=""/>
                    <meta name="description" content=""/>
                    <meta name="viewport" content="width=device-width, initial-scale=1"/>
                    <meta name="robot" content="index, follow"/>
                    <meta charSet="utf-8"/>

                    <link rel="stylesheet" href="/static/css/bootstrap.css"/>
                    <link rel="stylesheet" href="/static/css/layout.css" media="screen,projection"/>
                    <link rel="shortcut icon" href="/static/images/favicon.ico"/>
                    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
                          integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU"
                          crossOrigin="anonymous"/>
                    <link rel="stylesheet" href="/static/scrollbar-plugin/jquery.mCustomScrollbar.css"/>
                    <link rel="stylesheet" href="/static/css/input-radio-style.css"/>
                    <link rel="stylesheet" href="/static/flexslider/flexslider.css" media="screen"/>

                    <script src="/static/js/jquery-3.3.1.slim.min.js"/>
                    <script src="/static/js/jquery.min.js"/>
                    <script src="/static/js/jquery-ui.js"/>
                    <script src="/static/js/popper.min.js"/>
                    <script src="/static/js/bootstrap.min.js"/>

                    <script src="/static/flexslider/demo/js/modernizr.js"/>
                    <script defer src="/static/flexslider/jquery.flexslider.js"/>
                    <script src="/static/flexslider/demo/js/shCore.js"/>
                    <script src="/static/flexslider/demo/js/shBrushJScript.js"/>

                    <script src="/static/dotdotdot-master/src/js/jquery.dotdotdot.js"/>

                    {/*Add mousewheel plugin (this is optional)*/}
                    <script src="/static/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"/>

                    {/*Add fancyBox main JS and CSS files*/}
                    <script src="/static/fancybox/source/jquery.fancybox.js?v=2.1.5"/>
                    <link rel="stylesheet" href="/static/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen"/>

                    {/*Add Button helper (this is optional)*/}
                    <link rel="stylesheet" href="/static/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5"/>
                    <script src="/static/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"/>

                    {/*Add Thumbnail helper (this is optional)*/}
                    <link type="text/css" href="/static/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7"/>
                    <script src="/static/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"/>

                    {/*Add Media helper (this is optional)*/}
                    <script src="/static/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"/>
                    <script src="/static/scrollbar-plugin/js/minified/jquery-1.11.0.min.js"/>
                    <script src="/static/scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"/>
                </NextHead>

                <style jsx global>{`
                    
                `}</style>
            </div>
        );
    }
}