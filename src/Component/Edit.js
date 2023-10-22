import React, {useEffect, useState} from "react";
import {Col, Container, Row, Button} from "react-bootstrap";
import Stack from 'react-bootstrap/Stack';
import {Select} from 'react-ui'
import apiFetch from "@wordpress/api-fetch";
import Toast from 'react-bootstrap/Toast';
import ToastContainer from 'react-bootstrap/ToastContainer';

const Edit = ({}) => {

    const [routes, setRoutes] = useState([]);
    const [routeKeys, setRouteKeys] = useState([]);
    const [selectedRoute, setSelectedRoute] = useState();
    const [isError, setIsError] = useState( false );
    const [isLoading, setLoading] = useState(false);

    const findPositionalArguments = ( inputString ) => {
        const regex = /(\?P<parent>\[\\d]\+)|(\?P<id>\[\\d]\+)|(\?P<status>\[\\w-]+)|(\?P<taxonomy>\[\\w-]+)/g;
        const match = inputString.match(regex);
        console.log( inputString );
        console.log( match );
        if (match && match[1]) {
            const id = match[1];
            console.log(id);
        }
    }

    const handleClick = () => setLoading(true);

    const ErrorToast = () => {
        return (
            <ToastContainer
                className="p-3"
                position={'top-end'}
                style={{zIndex: 1}}
            >
                <Toast onClose={() => setIsError(false)}>
                    <Toast.Header>
                        <strong className="me-auto">Error</strong>
                    </Toast.Header>
                    <Toast.Body>
                        Unable to Fetch data from the end point,<br/>
                        It might be of 2 reasons,<br/>
                        1. Endpoint Not available or<br/>
                        2. Provided endpoint is not a data source.<br/>
                        or Missing some configuration.<br/>
                    </Toast.Body>
                </Toast>
            </ToastContainer>
        );
    }

    const fetchRoutes = () => {
        setIsError(false);
        apiFetch({path: '/wpcc/v1/routes'}).then(
            (data) => {
                setRoutes(data);
            },
            (error) => {
                setIsError(true);
            }
        )
    }

    const fetchRouteKeys = () => {
        setIsError(false);
        findPositionalArguments( selectedRoute );
        apiFetch({path: selectedRoute + '/reports?inline_data=1'}).then(
            (data) => {
                setRouteKeys(data);
            },
            (error) => {
                setIsError(true);
            }
        );
    }

    useEffect(() => {
        if (selectedRoute) {
            fetchRouteKeys();
        }
    }, [selectedRoute])

    useEffect(() => {
        fetchRoutes();
    }, [])

    return <Container>
        { isError && <ErrorToast /> }
        <Row>
            <Col>
                <Stack direction="horizontal" gap={3}>
                    <Select onChange={(event) => setSelectedRoute(event.target.value)}>
                        <option>Please select an Endpoint</option>
                        {routes &&
                            routes.map((name) => {
                                return <option key={name}>{name}</option>
                            })
                        }
                    </Select>
                    <input type={'text'} name={'positional_args_1'} placeholder={'Positional Arguments 1'} />
                    <input type={'text'} name={'positional_args_1'} placeholder={'Positional Arguments 2'} />
                    <Button
                        variant="secondary"
                        disabled={isLoading}
                        onClick={ !isLoading ? handleClick : null}
                    >
                        {isLoading ? 'Loading…' : 'Click to load'}
                    </Button>
                </Stack>
            </Col>
        </Row>
        <Row className={'wpcc-rest-keys'}>
            <Col>

            </Col>
        </Row>
    </Container>
}

export default Edit;
