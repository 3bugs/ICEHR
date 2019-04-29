import Head from '../components/Head';
import Header from '../components/Header';

export default class MainLayout extends React.Component {
    render() {
        return (
            <div style={layoutStyle}>
                <Head />
                <Header />
                {this.props.children}
            </div>
        );
    }
}

const layoutStyle = {

};