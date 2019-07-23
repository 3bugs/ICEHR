import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
//import $ from 'jquery';
import fetch from 'isomorphic-unfetch';
import {HOST_BACKEND} from '../etc/constants';
import {getDateFormatFromDateObject, getDateDisplayFromDateObject} from '../etc/utils';

export default class About extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
        };
    }

    /*static getInitialProps = async ({req, query}) => {
        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';

        const res = await fetch(baseUrl + '/api/xxxxxx', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({}),
        });

        const result = await res.json();
        if (result.error.code === 0) {
            return {
                dataList: xxx,
            };
        } else {
            return {
                dataList: null,
            };
        }
    };*/

    componentDidMount() {
    }

    render() {
        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <React.Fragment>

                </React.Fragment>

                <style jsx>{`
                `}</style>
            </MainLayout>
        );
    }
}