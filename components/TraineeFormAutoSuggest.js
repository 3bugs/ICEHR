import AutoSuggest from 'react-autosuggest';
import './TraineeFormAutoSuggest.css';
import {getLoginUser, formatCourseDateShort} from "../etc/utils";
import fetch from 'isomorphic-unfetch';

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

  getSuggestionValue = suggestion => {
    this.props.onClickSuggestItem(suggestion.traineeFormNumber);
    return suggestion.traineeFormNumber;
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

  handleOnChange = (event, {newValue, method}) => {
    let inputFormNumber = newValue.trim().replace(/-/g, '').toUpperCase();

    if (inputFormNumber.length === 1) {
      const startLetter = inputFormNumber.substring(0, 1);
      if (startLetter !== 'A' && startLetter !== 'S' && startLetter !== 'D') {
        alert("เลขที่ใบสมัคร ต้องขึ้นต้นด้วย 'AC' หรือ 'SO' หรือ 'DL' เท่านั้น");
        return;
      }
    } else if (inputFormNumber.length === 2) {
      const startTwoLetters = inputFormNumber.substring(0, 2);
      if (startTwoLetters !== 'AC' && startTwoLetters !== 'SO' && startTwoLetters !== 'DL') {
        alert("เลขที่ใบสมัคร ต้องขึ้นต้นด้วย 'AC' หรือ 'SO' หรือ 'DL' เท่านั้น");
        return;
      }
    } else if (inputFormNumber.length === 3) {
      if (isNaN(parseInt(inputFormNumber.substring(2)))) {
        alert("เลขที่ใบสมัคร ต้องขึ้นต้นด้วย 'AC' หรือ 'SO' หรือ 'DL' เท่านั้น แล้วตามด้วยตัวเลข 8 หรือ 12 หลัก");
        return;
      }
    }

    if ((inputFormNumber.substring(0, 2) === 'AC' && inputFormNumber.length > 14)
      || (inputFormNumber.substring(0, 2) === 'SO' && inputFormNumber.length > 10)
      || (inputFormNumber.substring(0, 2) === 'DL' && inputFormNumber.length > 10)) {
      return;
    }
    inputFormNumber = this.insertDashAtPosition(inputFormNumber, 2);
    inputFormNumber = this.insertDashAtPosition(inputFormNumber, 7);
    inputFormNumber = this.insertDashAtPosition(inputFormNumber, 12);

    this.props.onChange(inputFormNumber);

    if (method === 'enter') {
      //alert('test');
      //this.props.onClickSuggestItem(inputFormNumber);
    }
  }

  insertDashAtPosition(source, position) {
    if (source.length > position) {
      return [source.slice(0, position), '-', source.slice(position)].join('');
    }
    return source;
  }

  render() {
    const {value, suggestions} = this.state;
    const inputProps = {
      placeholder: 'กรอกเลขที่ใบสมัคร',
      value: this.props.value,
      onChange: this.handleOnChange,
    };

    return (
      <React.Fragment>
        <AutoSuggest
          multiSection={true}
          suggestions={suggestions}
          onSuggestionsFetchRequested={this.onSuggestionsFetchRequested}
          onSuggestionsClearRequested={this.onSuggestionsClearRequested}
          getSuggestionValue={this.getSuggestionValue}
          renderSuggestion={renderSuggestion}
          renderSectionTitle={renderSectionTitle}
          getSectionSuggestions={getSectionSuggestions}
          shouldRenderSuggestions={shouldRenderSuggestions}
          inputProps={inputProps}/>
        <ul style={{marginTop: '10px'}}>
          <li>เลขที่ใบสมัคร จะขึ้นต้นด้วย 'AC' หรือ 'SO' หรือ 'DL' แล้วตามด้วยตัวเลข 8 หรือ 12 หลัก</li>
          <li>ตัวอย่างเช่น AC<span style={{color: '#b50303'}}>-</span>2020<span style={{color: '#b50303'}}>-</span>0001<span style={{color: '#b50303'}}>-</span>0001</li>
          <li>ไม่ต้องกรอกเครื่องหมาย <span style={{color: '#b50303'}}>-</span> (ระบบจะเติมให้เอง)</li>
        </ul>

        <style jsx>{`

                `}</style>
      </React.Fragment>
    );
  }
}
