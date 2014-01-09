Supported Mutations
-------------------

OPERATORS

1. BooleanAnd - replaces && with ||
2. BooleanOr - replaces || with &&
3. BooleanFalse - replaces FALSE with TRUE
4. BooleanTrue - replaces TRUE with FALSE
5. OperatorAddition - replaces + with -
6. OperatorSubtraction - replaces - with +
7. OperatorDecrement - replaces -- with ++
8. OperatorIncrement - replaces ++ with --
9. OperatorArithmeticDivision - replaces / with *
23. OperatorComparisonNotEqual - replaces != with ==

SCALARS

38. ScalarString - replaces a literal string with random string
40. ScalarInteger - replace an integer with random integer

LOGICAL

43. LogicalIf - replaces positive IF condition with a negative one

TODO - OPERATORS

10. OperatorArithmeticModulus - replaces % with *
11. OperatorArithmeticMultiplication - replaces * with /
12. OperatorNegation - replaces - with <blank>
13. OperatorAssignmentPlusEqual - replaces += with -=
14. OperatorAssignmentMinusEqual - replaces -= with +=
15. OperatorBitwiseAnd - replaces & with |
16. OperatorBitwiseOr - replaces | with &
17. OperatorBitwiseXor - replaces ^ with |
18. OperatorBitwiseNot - replaces ~ with <blank>
19. OperatorBitwiseShiftLeft - replaces << with >>
20. OperatorBitwiseShiftRight - replaces >> with <<
21. OperatorComparisonEqual - replaces == with !=
22. OperatorComparisonIdentical - replaces === with !==
24. OperatorComparisonNotEqualAngular - replaces <> with ==
25. OperatorComparisonNotIdentical - replaces !== with ===
26. OperatorComparisonLessThan - replaces < with >
27. OperatorComparisonGreaterThan - replaces > with <
28. OperatorComparisonLessThenOrEqualTo - replaces <= with >
29. OperatorComparisonGreaterThanOrEqualTo - replaces >= with <
30. OperatorLogicalAnd - replaces AND with Or
31. OperatorLogicalOr - replaces OR with AND
32. OperatorLogicalXor - replaces XOR with AND
33. OperatorLogicalNot - replaces ! with <blank>
*34. OperatorLogicalAndSym - replaces && with || <replaces 1>
*35. OperatorLogicalOrSym - replaces || with && <replaces 2>
36. OperatorTypeInstanceof - replaces instanceof with !instanceof
*37. OperatorTypeNotInstanceOf - <covered by OperatorLogicalNot>

TODO - SCALARS

39. ScalarRegex - replaces a regex with random regex
41. ScalarFloat - replace a float with random float
42. ScalarNumeric - replace a string if numeric with a random numeric string

TODO - LOGICAL

*44. LogicalIfNegative - <covered by OperatorLogicalNot>

TODO - RENAMES

