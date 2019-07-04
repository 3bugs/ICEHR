import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
//import $ from 'jquery';

export default class Test extends React.Component {

    componentDidMount = () => {
        $('#result').text('PHP');

        $('#carousel').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: false,
            slideshow: false,
            itemWidth: 100,
            itemMargin: 5,
            asNavFor: '#slider'
        });

        $('#slider').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: false,
            slideshow: false,
            sync: "#carousel"
        });
    };

    onClickButton = () => {
        $('#result').text('TEST');
    };

    render() {
        return (
            <MainLayout>
                <NextHead>
                    <link rel="stylesheet" href="/static/flexslider2/flexslider.css" type="text/css" media="screen"/>
                    <script defer src="/static/flexslider2/jquery.flexslider.js"/>
                </NextHead>

                <div className="row">
                    <div className="col-md-4">
                        <div id={'result'}/>
                        <button onClick={this.onClickButton}>TEST</button>

                        <div id="slider" class="flexslider">
                            <ul class="slides">
                                <li>
                                    <img src="/static/images/image-test.png"/>
                                </li>
                                <li>
                                    <img src="/static/images/image-test.png"/>
                                </li>
                                <li>
                                    <img src="/static/images/image-test.png"/>
                                </li>
                                <li>
                                    <img src="/static/images/image-test.png"/>
                                </li>
                            </ul>
                        </div>
                        <div id="carousel" class="flexslider">
                            <ul class="slides">
                                <li>
                                    <img src="/static/images/image-test.png"/>
                                </li>
                                <li>
                                    <img src="/static/images/image-test.png"/>
                                </li>
                                <li>
                                    <img src="/static/images/image-test.png"/>
                                </li>
                                <li>
                                    <img src="/static/images/image-test.png"/>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <style jsx>{`

                `}</style>
            </MainLayout>
        );
    }
}