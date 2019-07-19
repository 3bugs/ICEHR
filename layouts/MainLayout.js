import Head from '../components/Head';
import Header from '../components/Header';
import Footer from "../components/Footer";

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