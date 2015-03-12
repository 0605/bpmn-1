# KoolKode BPMN 2.0 Process Engine

[![Build Status](https://travis-ci.org/koolkode/bpmn.svg?branch=master)](https://travis-ci.org/koolkode/bpmn)

**This project is not ready for production usage yet.**

Provides a basic process engine that can load BPMN 2.0 diagrams and execute contained processes. The BPMN engine
requires a relational database that is supported by KoolKode Database in order to persist process definitions, instances
and other runtime data. Like [Activiti](http://activiti.org/) it makes good use of the command pattern during execution
of a process instance.
