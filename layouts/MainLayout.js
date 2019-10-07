import Head from '../components/Head';
import Header from '../components/Header';
import Footer from "../components/Footer";
import NextHead from "next/head";
import $ from 'jquery';
import fetch from "isomorphic-unfetch";
import { initGA, logPageView } from '../etc/analytics.js';

export default class MainLayout extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    static doGetService = async () => {

    };

    componentDidMount_Old() {
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
                    /*const services = {};
                    serviceList.map(item => {
                        services[item.slug] = {
                            title: item.title,
                            details: item.details,
                            url: item.url,
                        };
                    });*/

                    fetch('/api/get_intro', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            type: 'link',
                        }),
                    })
                        .then(resultLink => resultLink.json())
                        .then(resultLink => {
                            if (resultLink.error.code === 0) {
                                const linkList = resultLink.dataList;

                                this.setState({
                                    serviceList,
                                    linkList,
                                    //services,
                                });
                            } else {
                                this.setState({
                                    serviceList: null,
                                    linkList: null,
                                    //services: null,
                                });
                            }
                        });
                } else {
                    this.setState({
                        serviceList: null,
                        linkList: null,
                        //services: null,
                    });
                }
            });
    }

    componentDidMount() {
        //Google Analytics
        if (!window.GA_INITIALIZED) {
            initGA();
            window.GA_INITIALIZED = true;
        }
        logPageView();

        fetch('/api/get_service_link_contact', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({}),
        })
            .then(result => result.json())
            .then(result => {
                if (result.error.code === 0) {
                    const {serviceList, linkList, contactList, socialList} = result;

                    this.setState({
                        serviceList,
                        linkList,
                        contactList,
                        socialList,
                    });
                } else {
                    this.setState({
                        serviceList: null,
                        linkList: null,
                        contactList: null,
                        socialList: null,
                    });
                }
            });
    }

    render() {
        const {serviceList, linkList, contactList, socialList} = this.state;

        return (
            <div style={layoutStyle}>
                <Head/>
                <Header
                    serviceList={serviceList}
                    linkList={linkList}/>
                {this.props.children}
                <Footer
                    serviceList={serviceList}
                    contactList={contactList}
                    socialList={socialList}/>
            </div>
        );
    }
}

const layoutStyle = {};