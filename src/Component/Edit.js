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
import Spinner from 'react-bootstrap/Spinner';
import {__} from '@wordpress/i18n';

const Edit = ({}) => {

    const [routes, setRoutes] = useState([]);
    const [routeKeys, setRouteKeys] = useState([]);
    const [selectedRoute, setSelectedRoute] = useState();
    const [isError, setIsError] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const [isSaved, setIsSaved] = useState(false);

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
        setIsSaved(false);
        setLoading(true);
        apiFetch({
            path: '/wpcc/v1/routes/save',
            method: 'POST',
            data: {
                route: selectedRoute + "/reports",
                data: routeKeys,
                _nonce: window.AcjWpccBlocksEditorSettings.nonce_save
            }
        }).then((data) => {
            setIsSaved(true);
            setLoading(false);
        }, (error) => {
            setIsError(true);
        })
    };

    const SuccessToast = ({data}) => {
        return (<ToastContainer
            className="p-3"
            position={'top-end'}
            style={{zIndex: 1}}
        >
            <Toast bg={'success'} onClose={() => setIsSaved(false)}>
                <Toast.Header>
                    <strong className="me-auto">Data Saved</strong>
                </Toast.Header>
                <Toast.Body>{data}</Toast.Body>
            </Toast>
        </ToastContainer>);
    }

    const ErrorToast = () => {
        return (<ToastContainer
            className="p-3"
            position={'top-end'}
            style={{zIndex: 1}}
        >
            <Toast bg={'danger'} onClose={() => setIsError(false)}>
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
        apiFetch({path: '/wpcc/v1/routes?_nonce=' + window.AcjWpccBlocksEditorSettings.nonce_get}).then((data) => {
            setRoutes(data);
        }, (error) => {
            setIsError(true);
        })
    }

    const fetchRouteKeys = () => {
        setLoading(true);
        setIsError(false);
        findPositionalArguments(selectedRoute);
        setRouteKeys([]);
        apiFetch({path: selectedRoute + "/" + window.AcjWpccBlocksEditorSettings.route_endpoint + "/?skeleton=1&skeleton_type=1"}).then((data) => {
            setRouteKeys(data);
            setLoading(false);
        }, (error) => {
            setIsError(true);
            setLoading(false);
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

    const Loader = () => {
        return <Spinner animation="border" role="status">
            <span className="visually-hidden">{__('Loading...', 'acjwp-community-connector')}</span>
        </Spinner>
    }

    return <Container>
        {isError && <ErrorToast/>}
        {isSaved && <SuccessToast data={'Data Saved Successfully.'}/>}
        <Row>
            <Col md={12}>
                <Stack direction="horizontal" gap={3}>
                    <Select onChange={(event) => setSelectedRoute(event.target.value)}>
                        <option>{__('Please select an Endpoint', 'acjwp-community-connector')}</option>
                        {routes && routes.map((name) => {
                            return <option key={name}>{name}</option>
                        })}
                    </Select>
                    <Button
                        variant="secondary"
                        disabled={isLoading}
                        onClick={!isLoading ? handleClick : null}
                    >
                        {isLoading ? __('Loading...', 'acjwp-community-connector') : __(buttonText, 'acjwp-community-connector')}
                    </Button>
                </Stack>
            </Col>
            <Col md={12} style={{marginTop: '30px'}}>
                <Table striped bordered hover>
                    <thead>
                    <tr>
                        <th>{__('#id', 'acjwp-community-connector')}</th>
                        <th>{__('Name', 'acjwp-community-connector')}</th>
                        <th>{__('Description', 'acjwp-community-connector')}</th>
                        <th>{__('Type', 'acjwp-community-connector')}</th>
                        <th>{__('Aggregation', 'acjwp-community-connector')}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {isLoading && <tr>
                        <td>{isLoading && <Loader/>}</td>
                        <td>{isLoading && <Loader/>}</td>
                        <td>{isLoading && <Loader/>}</td>
                        <td>{isLoading && <Loader/>}</td>
                        <td>{isLoading && <Loader/>}</td>
                    </tr>
                    }
                    {!isLoading && routeKeys && Object.entries(routeKeys).map((value, key) => {

                        return <tr key={value[0]}>
                            <td>{__(value[0], 'acjwp-community-connector')}</td>
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
                                        rows={1}
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
                                    <option value="NUMBER">Number (14)</option>
                                    <option value="PERCENT">Percentage (can be over 1.0)</option>
                                    <option value="TEXT">TEXT</option>
                                    <option value="BOOLEAN">BOOLEAN (true or false)</option>
                                    <optgroup label="Link group">
                                        <option value="URL">URL ("https://www.google.com")</option>
                                        <option value="HYPERLINK">HYPERLINK (A link with a text label)</option>
                                        <option value="IMAGE">Image (A URL of an image)</option>
                                        <option value="IMAGELINK">IMAGE LINK (A link with an image label)</option>
                                    </optgroup>
                                    <optgroup label="DateTime group">
                                        <option value="YEAR">YEAR ("2017")</option>
                                        <option value="YEAR_QUARTER">YEAR QUARTER ("20171")</option>
                                        <option value="YEAR_MONTH">YEAR MONTH ("201703")</option>
                                        <option value="YEAR_WEEK">YEAR WEEK ("201707")</option>
                                        <option value="YEAR_MONTH_DAY">YEAR MONTH DAY ("20170317")</option>
                                        <option value="YEAR_MONTH_DAY_HOUR">YEAR MONTH DAY HOUR (2017031403)</option>
                                        <option value="YEAR_MONTH_DAY_SECOND">YEAR MONTH DAY SECOND (20170314031545)
                                        </option>
                                        <option value="QUARTER">QUARTER (1, 2, 3, 4)</option>
                                        <option value="MONTH">MONTH (03)</option>
                                        <option value="WEEK">WEEK (07)</option>
                                        <option value="MONTH_DAY">MONTH DAY (0317)</option>
                                        <option value="DAY_OF_WEEK">DAY OF WEEK (A decimal number 0-6 with 0
                                            representing Sunday)
                                        </option>
                                        <option value="DAY">DAY (17)</option>
                                        <option value="HOUR">HOUR (02)</option>
                                        <option value="MINUTE">MINUTE (12)</option>
                                        <option value="DURATION">DURATION ( A Duration of Time in seconds )</option>
                                    </optgroup>
                                    <optgroup label="Geo Group">
                                        <option value="COUNTRY">COUNTRY ("United States")</option>
                                        <option value="COUNTRY_CODE">COUNTRY_CODE ("US")</option>
                                        <option value="CONTINENT">CONTINENT ("Americas")</option>
                                        <option value="CONTINENT_CODE">CONTINENT CODE ("019")</option>
                                        <option value="SUB_CONTINENT">Sub Continent ("North America")</option>
                                        <option value="SUB_CONTINENT_CODE">Sub Continent Code ("003")</option>
                                        <option value="REGION">Region ("California")</option>
                                        <option value="REGION_CODE">Region Code ("CA")</option>
                                        <option value="CITY">City ("Mountain View")</option>
                                        <option value="CITY_CODE">City Code ("1014044")</option>
                                        <option value="METRO_CODE">Metro Code ("200807")</option>
                                        <option value="LATITUDE_LONGITUDE">Latitude and Longitude ("51.5074, -0.1278")
                                        </option>
                                    </optgroup>
                                </Form.Select>
                            </td>
                            <td>
                                <Form.Select
                                    aria-label="Aggregation"
                                    defaultValue={value[1]['aggregation']}
                                    onChange={(e) => {
                                        value[1]['aggregation'] = e.target.value;
                                    }}
                                >
                                    <option>Select Aggregation</option>
                                    <option value="NONE">No aggregation</option>
                                    <option value="AUTO">AUTO (Should be set for calculated fields involving an
                                        aggregation)
                                    </option>
                                    <option value="SUM">SUM (The sum of the entries.)</option>
                                    <option value="MIN">MIN (The minimum of the entries.)</option>
                                    <option value="MAX">MAX (The maximum of the entries.)</option>
                                    <option value="COUNT">COUNT (The number of entries.)</option>
                                    <option value="COUNT_DISTINCT">COUNT_DISTINCT (The number of distinct entries.)
                                    </option>
                                    <option value="AVG">AVG (The numerical average (mean) of the entries.)</option>
                                </Form.Select>
                            </td>
                        </tr>
                    })}
                    </tbody>
                </Table>
            </Col>
        </Row>
        <Row className={'wpcc-rest-keys'}><Col></Col></Row>
    </Container>
}

export default Edit;
