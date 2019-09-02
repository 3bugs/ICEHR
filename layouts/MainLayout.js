import Head from '../components/Head';
import Header from '../components/Header';
import Footer from "../components/Footer";
import NextHead from "next/head";
import $ from 'jquery';
import fetch from "isomorphic-unfetch";

export default class MainLayout extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    static doGetService = async () => {

    };

    componentDidMount() {
        fetch('/api/get_service', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({}),
        })
            .then(result => result.json())
            .then(result => {
                if (result.error.code === 0) {
                    const {serviceList} = result;
                    const services = {};
                    serviceList.map(item => {
                        services[item.slug] = {
                            title: item.title,
                            details: item.details,
                            url: item.url,
                        };
                    });
                    this.setState({
                        services,
                    });
                } else {
                    this.setState({
                        services: null,
                    });
                }
            });
    }

    render() {
        const {services} = this.state;

        return (
            <div style={layoutStyle}>
                <Head/>
                <Header
                    services={services}/>
                {this.props.children}
                <Footer
                    services={services}/>
            </div>
        );
    }
}

const layoutStyle = {};