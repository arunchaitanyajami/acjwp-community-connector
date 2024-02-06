import 'bootstrap/dist/css/bootstrap.min.css'
import {createRoot} from 'react-dom/client';
import RootContainer from './Container/Edit';
import './styles.scss';
import { ThemeProvider } from 'react-ui'
import { tokens, components } from 'react-ui/themes/light'


// Render your React component instead.
const root          = createRoot( document.getElementById( 'acjwpcc-ui' ) );

const Main = () => {
    return <ThemeProvider tokens={tokens} components={components}><RootContainer/></ThemeProvider>
}
root.render(<Main />);
