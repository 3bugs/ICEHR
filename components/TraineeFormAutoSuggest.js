import AutoSuggest from 'react-autosuggest';
import './TraineeFormAutoSuggest.css';
import {getLoginUser, formatCourseDateShort} from "../etc/utils";
import fetch from 'isomorphic-unfetch';

/*const languages = [
    {
        title: '1970s',
        languages: [
            {
                name: 'C',
                year: 1972
            }
        ]
    },
    {
        title: '1980s',
        languages: [
            {
                name: 'C++',
                year: 1983
            },
            {
                name: 'Perl',
                year: 1987
            }
        ]
    },
    {
        title: '1990s',
        languages: [
            {
                name: 'Haskell',
                year: 1990
            },
            {
                name: 'Python',
                year: 1991
            },
            {
                name: 'Java',
                year: 1995
            },
            {
                name: 'Javascript',
                year: 1995
            },
            {
                name: 'PHP',
                year: 1995
            },
            {
                name: 'Ruby',
                year: 1995
            }
        ]
    },
    {
        title: '2000s',
        languages: [
            {
                name: 'C#',
                year: 2000
            },
            {
                name: 'Scala',
                year: 2003
            },
            {
                name: 'Clojure',
                year: 2007
            },
            {
                name: 'Go',
                year: 2009
            }
        ]
    },
    {
        title: '2010s',
        languages: [
            {
                name: 'Elm',
                year: 2012
            }
        ]
    }
];*/

// https://developer.mozilla.org/en/docs/Web/JavaScript/Guide/Regular_Expressions#Using_Special_Characters
function escapeRegexCharacters(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

/*function getSuggestions(value) {
    const escapedValue = escapeRegexCharacters(value.trim());

    if (escapedValue === '') {
        return [];
    }

    const regex = new RegExp('^' + escapedValue, 'i');

    return languages
        .map(section => {
            return {
                title: section.title,
                languages: section.languages.filter(language => regex.test(language.name))
            };
        })
        .filter(section => section.languages.length > 0);
}*/

function getSuggestionValue(suggestion) {
    return suggestion.traineeFormNumber;
}

function renderSuggestion(suggestion) {
    return (
        <span>{`${suggestion.traineeFormNumber}: `}
            <span style={{fontFamily: 'DBHeavent-Med', color: '#888'}}>{`${suggestion.traineeFirstName} ${suggestion.traineeLastName}`}</span>
        </span>
    );
}

function renderSectionTitle(section) {
    return (
        <strong>{`${section.courseName}`} - <span style={{color: '#333'}}>{`สมัคร ${formatCourseDateShort(section.registrationDateTime, section.registrationDateTime)}`}</span></strong>
    );
}

function getSectionSuggestions(section) {
    return section.traineeList;
}

function shouldRenderSuggestions() {
    return true;
}

export default class TraineeFormAutoSuggest extends React.Component {
    constructor() {
        super();

        this.state = {
            value: '',
            suggestions: [],
            registrationList: [],
        };
    }

    componentDidMount() {
        const user = getLoginUser();

        if (user !== null) {
            fetch('/api/get_registration_list_by_member', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    memberId: user.id
                }),
            })
                .then(result => result.json())
                .then(result => {
                    if (result['error']['code'] === 0) {
                        this.setState({
                            registrationList: result['dataList'],
                            errorMessage: null,
                        }, () => {
                            this.setState({
                                suggestions: this.getSuggestions('')
                            });
                        });
                    } else {
                        this.setState({
                            registrationList: null,
                            suggestions: null,
                            errorMessage: result['error']['message'],
                        });
                    }
                });
        } else { // กรณีไม่ได้ login

        }
    }

    onChange = (event, {newValue, method}) => {
        this.setState({
            value: newValue
        });
    };

    onSuggestionsFetchRequested = ({value}) => {
        this.setState({
            suggestions: this.getSuggestions(value)
        });
    };

    getSuggestions = (value) => {
        const escapedValue = escapeRegexCharacters(value.trim());

        if (escapedValue === '') {
            //return [];
        }

        //const regex = new RegExp('^' + escapedValue, 'i');
        const regex = new RegExp(escapedValue, 'i');

        if (this.state.registrationList == null) {
            return [];
        }

        return this.state.registrationList
            .map(reg => {
                return {
                    courseName: `${reg.courseTitle} รุ่นที่ ${reg.courseBatchNumber}`,
                    registrationDateTime: reg.registrationDateTime,
                    traineeList: reg.traineeList.filter(trainee => regex.test(trainee.traineeFormNumber))
                };
            })
            .filter(reg => reg.traineeList.length > 0);
    };

    onSuggestionsClearRequested = () => {
        this.setState({
            suggestions: []
        });
    };

    render() {
        const {value, suggestions} = this.state;
        const inputProps = {
            placeholder: 'กรอกเลขที่ใบสมัคร',
            value: this.props.value,
            onChange: this.props.onChange
        };

        return (
            <React.Fragment>
                <AutoSuggest
                    multiSection={true}
                    suggestions={suggestions}
                    onSuggestionsFetchRequested={this.onSuggestionsFetchRequested}
                    onSuggestionsClearRequested={this.onSuggestionsClearRequested}
                    getSuggestionValue={getSuggestionValue}
                    renderSuggestion={renderSuggestion}
                    renderSectionTitle={renderSectionTitle}
                    getSectionSuggestions={getSectionSuggestions}
                    shouldRenderSuggestions={shouldRenderSuggestions}
                    inputProps={inputProps}/>

                <style jsx>{`

                `}</style>
            </React.Fragment>
        );
    }
}
