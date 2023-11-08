import React, {useEffect, useState} from "react";
import {Col, Container, Row, Button} from "react-bootstrap";
import Stack from 'react-bootstrap/Stack';
import {Select} from 'react-ui'
import apiFetch from "@wordpress/api-fetch";
import Toast from 'react-bootstrap/Toast';
import ToastContainer from 'react-bootstrap/ToastContainer';
import Table from 'react-bootstrap/Table';
import InputGroup from 'react-bootstrap/InputGroup';
import Form from 'react-bootstrap/Form';

const Edit = ({}) => {

    const [routes, setRoutes] = useState([]);
    const [routeKeys, setRouteKeys] = useState([]);
    const [selectedRoute, setSelectedRoute] = useState();
    const [isError, setIsError] = useState(false);
    const [isLoading, setLoading] = useState(false);

    let buttonText = selectedRoute ? 'Save Data' : 'Select Route';

    const findPositionalArguments = (inputString) => {
        const regex = /(\?P<parent>\[\\d]\+)|(\?P<id>\[\\d]\+)|(\?P<status>\[\\w-]+)|(\?P<taxonomy>\[\\w-]+)/g;
        const match = inputString.match(regex);
        if (match && match[1]) {
            return match[1];
        }

        return false;
    }

    const handleClick = () => {
        setLoading(true);
        apiFetch({
            path: '/wpcc/v1/routes/save',
            method: 'POST',
            data: {
                route: selectedRoute + "/reports",
                data: routeKeys
            }
        }).then((data) => {
            setLoading(false);
        }, (error) => {
            setIsError(true);
        })
    };

    const ErrorToast = () => {
        return (<ToastContainer
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
        </ToastContainer>);
    }

    const fetchRoutes = () => {
        setIsError(false);
        setRoutes([]);
        apiFetch({path: '/wpcc/v1/routes'}).then((data) => {
            setRoutes(data);
        }, (error) => {
            setIsError(true);
        })
    }

    const fetchRouteKeys = () => {
        setIsError(false);
        findPositionalArguments(selectedRoute);
        setRouteKeys([]);
        apiFetch({path: selectedRoute + "/reports/?skeleton=1&skeleton_type=1"}).then((data) => {
            setRouteKeys(data);
        }, (error) => {
            setIsError(true);
        });
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
        {isError && <ErrorToast/>}
        <Row>
            <Col md={12}>
                <Stack direction="horizontal" gap={3}>
                    <Select onChange={(event) => setSelectedRoute(event.target.value)}>
                        <option>Please select an Endpoint</option>
                        {routes && routes.map((name) => {
                            return <option key={name}>{name}</option>
                        })}
                    </Select>
                    <Button
                        variant="secondary"
                        disabled={isLoading}
                        onClick={!isLoading ? handleClick : null}
                    >
                        {isLoading ? 'Loading…' : buttonText}
                    </Button>
                </Stack>
            </Col>
            <Col md={12} style={{marginTop: '30px'}}>
                <Table striped bordered hover>
                    <thead>
                    <tr>
                        <th>#id</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Type</th>
                        {/*<th>Aggregation</th>*/}
                    </tr>
                    </thead>
                    <tbody>
                    {routeKeys && Object.entries(routeKeys).map((value, key) => {
                        return <tr key={value[0]}>
                            <td>{value[0]}</td>
                            <td>
                                <InputGroup size="sm" className="mb-3">
                                    <Form.Control
                                        aria-label="Small"
                                        aria-describedby="inputGroup-sizing-sm"
                                        placeholder={`Name for ${value[0]}`}
                                        id={`${value[0]}-name`}
                                        defaultValue={value[1]['name']}
                                        onChange={(e) => {
                                            value[1]['name'] = e.target.value;
                                        }}
                                    />
                                </InputGroup>
                            </td>
                            <td>
                                <Form.Group className="mb-3" controlId={`${value[0]}.ControlTextarea`}>
                                    <Form.Control
                                        as="textarea"
                                        rows={3}
                                        defaultValue={value[1]['description']}
                                        onChange={(e) => {
                                            value[1]['description'] = e.target.value;
                                        }}
                                    />
                                </Form.Group>
                            </td>
                            <td>
                                <Form.Select
                                    aria-label="Type"
                                    defaultValue={value[1]['type']}
                                    onChange={(e) => {
                                        value[1]['type'] = e.target.value;
                                    }}
                                >
                                    <option>Select Type</option>
                                    <option value="integer">Integer</option>
                                    <option value="url">URL</option>
                                    <option value="string">String</option>
                                    <option value="boolean">Boolean</option>
                                </Form.Select>
                            </td>
                            {/*<td></td>*/}
                        </tr>
                    })}
                    </tbody>
                </Table>
            </Col>
        </Row>
        <Row className={'wpcc-rest-keys'}>
            <Col>

            </Col>
        </Row>
    </Container>
}

export default Edit;
