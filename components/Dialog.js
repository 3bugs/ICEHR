import Modal from 'react-modal';

const customStyles = {
    content: {
        top: '50%',
        left: '50%',
        right: 'auto',
        bottom: 'auto',
        marginRight: '-50%',
        transform: 'translate(-50%, -50%)',
        paddingLeft: '25px',
        paddingRight: '25px',
        minWidth: '200px',
    }
};

// Make sure to bind modal to your appElement (http://reactcommunity.org/react-modal/accessibility/)
//Modal.setAppElement('#yourAppElement');

export default class Dialog extends React.Component {
    constructor() {
        super();

        this.state = {
            modalIsOpen: false,
            message: '',
            textColor: '#000',
            onCloseCallback: null,
        };

        this.openModal = this.openModal.bind(this);
        this.afterOpenModal = this.afterOpenModal.bind(this);
        this.closeModal = this.closeModal.bind(this);
    }

    resolveTextColor = (color) => {
        if (color === 'ok' || color === 'success') {
            return '#186A3B';
        } else if (color === 'failed' || color === 'error') {
            return '#f00';
        }
        return color;
    };

    componentDidMount() {
        const {isOpen, message, textColor, onCloseCallback} = this.props;
        this.setState({
            modalIsOpen: isOpen,
            message,
            textColor: this.resolveTextColor(textColor),
            onCloseCallback
        });
    }

    componentWillReceiveProps(nextProps) {
        const {isOpen, message, textColor, onCloseCallback} = nextProps;
        this.setState({
            modalIsOpen: isOpen,
            message,
            textColor: this.resolveTextColor(textColor),
            onCloseCallback
        });
    }

    openModal() {
        this.setState({modalIsOpen: true});
    }

    afterOpenModal() {
        // references are now sync'd and can be accessed.
        //this.subtitle.style.color = '#f00';
        this.subtitle.style.color = this.state.textColor;
    }

    closeModal() {
        const {onCloseCallback} = this.state;
        this.setState({modalIsOpen: false}, () => {
            if (onCloseCallback) {
                onCloseCallback();
            }
        });
    }

    render() {
        const {message} = this.state;

        return (
            <div>
                {/*<button onClick={this.openModal}>Open Modal</button>*/}
                <Modal
                    isOpen={this.state.modalIsOpen}
                    onAfterOpen={this.afterOpenModal}
                    onRequestClose={this.closeModal}
                    style={customStyles}
                    contentLabel="Example Modal"
                >
                    <h5 ref={subtitle => this.subtitle = subtitle}>{message}</h5>
                    <button onClick={this.closeModal}
                            className="mt-4"
                            style={{display: 'block', marginLeft: 'auto', marginRight: 'auto', minWidth: '80px'}}>
                        OK
                    </button>
                </Modal>
            </div>
        );
    }
}
