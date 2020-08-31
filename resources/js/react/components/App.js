import React, { useEffect, useState } from 'react';
import BackgroundSection from './BackgroundSection';
import Button from './Button';

const App = () => {
    const [ text, setText ] = useState('Click here');
    const [ status, setStatus ] = useState('idle');

    useEffect(() => {
        if (status === 'loading') {
            fetch('/api/country')
                .then(res => res.json())
                .then(data => data.status === 'success'
                    ? setText(data.country)
                    : Promise.reject()
                )
                .catch(() => setText('Request failed.'))
                .finally(() => setStatus('idle'));
        }
    }, [status]);

    return (
        <>
            <BackgroundSection id="top" />
            <BackgroundSection id="bottom" />
            <Button
                text={text}
                spinner={status === 'loading'}
                onClick={() => setStatus('loading')}
            />
        </>
    );
};

export default App;
