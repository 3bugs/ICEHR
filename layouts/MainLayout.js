import Head from '../components/Head';
import Header from '../components/Header';
import Footer from "../components/Footer";
import NextHead from "next/head";
import $ from 'jquery';

export default class MainLayout extends React.Component {
    render() {
        return (
            <div style={layoutStyle}>
                <Head />
                <Header />
                {this.props.children}
                <Footer/>
            </div>
        );
    }
}

const layoutStyle = {

};