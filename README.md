Prooph\Link\ProcessorProxy
==========================
Processor proxy for [prooph LINK](https://github.com/prooph/link)

# HTTP Process Trigger
The processor proxy module offers a way to start a process via http request.

## Collect Data Trigger
To use this endpoint the process should be startable via a `collect-data` workflow message. What you need to do is send a POST request to the `prooph.link/processor_proxy/api/collect_data_triggers` route defined in your prooph LINK application. By default this route name resolves to the URI `/prooph/link/processor-proxy/api/collect-data-triggers`.

### Request
The POST request should have a JSON body containing following structure:

```javascirpt
{
  "collect_data_trigger" : {
    "processing_type" : "Processing\Type\Type"
  }
}
```

As you can see the root key "collect_data_trigger" contains only a "processing_type" definition. The type should point to the `Processing\Type\Type` implementation that should be collected by the first task of the process.

### Response
The service respond with a status code 201 and without a body. Instead the header includes a `Location` pointing to a message information service with the UUID of the generated workflow message. The message information service provides detailed information about the status of the message, the triggered process and potentially occurred errors. 

# Message Service

## GET Message

- GET `/prooph/link/processor-proxy/api/messages[/:id]` (route: prooph.link/processor_proxy/api/messages)

Get information about the message identified by its UUID. 


## Messagebox

- POST `/prooph/link/processor-proxy/api/messages` (route: prooph.link/processor_proxy/api/messages)

The message service provides the possibility to receive a [service bus message](https://github.com/prooph/service-bus) which is then passed to the workflow engine. The endpoint is mostly used when a sub process or workflow message handler should be triggered via http. In this case the main workflow processor can send a workflow message via the [psb-http-dispatcher](https://github.com/prooph/psb-http-dispatcher) to this endpoint.

# Support

- Ask any questions on [prooph-users](https://groups.google.com/forum/?hl=de#!forum/prooph) google group.
- File issues at [https://github.com/prooph/link-processor-proxy/issues](https://github.com/prooph/link-processor-proxy/issues).

# Contribution

You wanna help us? Great!
We appreciate any help, be it on implementation level, UI improvements, testing, donation or simply trying out the system and give us feedback.
Just leave us a note in our google group linked above and we can discuss further steps.

Thanks,
your prooph team


