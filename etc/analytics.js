import ReactGA from 'react-ga';

export const initGA = () => {
    console.log('GA init');
    //ReactGA.initialize('UA-149505783-1');
    ReactGA.initialize('UA-151223867-1');
};
export const logPageView = () => {
    console.log(`Logging page view for ${window.location.pathname}`);
    ReactGA.set({ page: window.location.pathname });
    ReactGA.pageview(window.location.pathname);
};
export const logEvent = (category = '', action = '') => {
    if (category && action) {
        ReactGA.event({ category, action });
    }
};
export const logException = (description = '', fatal = false) => {
    if (description) {
        ReactGA.exception({ description, fatal });
    }
};