export default class ErrorLabel extends React.Component {
    constructor(props, context) {
        super(props, context);
    }

    static defaultProps = {
        textAlign: 'left',
        marginTop: 0,
        marginBottom: 0,
        marginLeft: 0,
        marginRight: 0,
        paddingLeft: '2px',
        paddingRight: '2px',
    };

    render() {
        return (
            <div style={{
                color: "red",
                fontSize: '0.9rem',
                display: this.props.value === undefined ? 'none' : 'block',
                textAlign: this.props.textAlign,
                marginTop: this.props.marginTop,
                marginBottom: this.props.marginBottom,
                marginLeft: this.props.marginLeft,
                marginRight: this.props.marginRight,
                paddingLeft: this.props.paddingLeft,
                paddingRight: this.props.paddingRight,
            }}>{this.props.value}</div>
        );
    }
}